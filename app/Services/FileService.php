<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileService
{
    /**
     * أنواع الملفات المدعومة
     */
    const TYPE_IMAGE = 'image';
    const TYPE_DOCUMENT = 'document';
    const TYPE_SPREADSHEET = 'spreadsheet';
    const TYPE_PDF = 'pdf';
    const TYPE_ARCHIVE = 'archive';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_OTHER = 'other';
    
    /**
     * مجلدات التخزين
     */
    const FOLDER_PRODUCTS = 'products';
    const FOLDER_CATEGORIES = 'categories';
    const FOLDER_USERS = 'users';
    const FOLDER_CUSTOMERS = 'customers';
    const FOLDER_ORDERS = 'orders';
    const FOLDER_INVOICES = 'invoices';
    const FOLDER_RETURNS = 'returns';
    const FOLDER_IMPORTS = 'imports';
    const FOLDER_EXPORTS = 'exports';
    const FOLDER_TEMP = 'temp';
    
    /**
     * @var LogService
     */
    protected $logService;
    
    /**
     * إنشاء مثيل جديد من الخدمة
     *
     * @param LogService $logService
     */
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }
    
    /**
     * تحميل ملف
     *
     * @param UploadedFile $file الملف المراد تحميله
     * @param string $folder المجلد المراد التخزين فيه
     * @param string|null $filename اسم الملف (اختياري)
     * @param bool $public هل الملف عام أم خاص
     * @param array $options خيارات إضافية
     * @return array معلومات الملف المحمل
     */
    public function uploadFile(
        UploadedFile $file,
        string $folder,
        ?string $filename = null,
        bool $public = true,
        array $options = []
    ): array {
        // إنشاء اسم ملف فريد إذا لم يتم تحديده
        if ($filename === null) {
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
        }
        
        // تحديد مسار التخزين
        $disk = $public ? 'public' : 'local';
        $path = $folder . '/' . $filename;
        
        // تحميل الملف
        $file->storeAs($folder, $filename, $disk);
        
        // الحصول على معلومات الملف
        $fileInfo = [
            'name' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'folder' => $folder,
            'disk' => $disk,
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => $this->getFileType($file),
            'url' => $public ? Storage::disk($disk)->url($path) : null,
        ];
        
        // تسجيل النشاط
        $this->logService->logActivity(
            "Uploaded file: {$fileInfo['original_name']}",
            'file',
            null,
            $fileInfo,
            LogService::ACTIVITY_TYPE_CREATE
        );
        
        return $fileInfo;
    }
    
    /**
     * تحميل صورة مع إمكانية تغيير حجمها
     *
     * @param UploadedFile $file الملف المراد تحميله
     * @param string $folder المجلد المراد التخزين فيه
     * @param string|null $filename اسم الملف (اختياري)
     * @param bool $public هل الملف عام أم خاص
     * @param array $options خيارات إضافية
     * @return array معلومات الملف المحمل
     */
    public function uploadImage(
        UploadedFile $file,
        string $folder,
        ?string $filename = null,
        bool $public = true,
        array $options = []
    ): array {
        // التحقق من أن الملف هو صورة
        if (!$this->isImage($file)) {
            throw new \InvalidArgumentException('The uploaded file is not an image.');
        }
        
        // إنشاء اسم ملف فريد إذا لم يتم تحديده
        if ($filename === null) {
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
        }
        
        // تحديد مسار التخزين
        $disk = $public ? 'public' : 'local';
        $path = $folder . '/' . $filename;
        
        // معالجة الصورة إذا تم تحديد خيارات التحجيم
        if (isset($options['resize']) && is_array($options['resize'])) {
            $width = $options['resize']['width'] ?? null;
            $height = $options['resize']['height'] ?? null;
            $maintainAspectRatio = $options['resize']['maintain_aspect_ratio'] ?? true;
            
            // فتح الصورة باستخدام مكتبة Intervention Image
            $image = Image::make($file);
            
            // تغيير حجم الصورة
            if ($width && $height) {
                if ($maintainAspectRatio) {
                    $image->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $image->resize($width, $height);
                }
            } elseif ($width) {
                $image->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } elseif ($height) {
                $image->resize(null, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            
            // حفظ الصورة المعالجة
            $fullPath = Storage::disk($disk)->path($path);
            $image->save($fullPath);
        } else {
            // تحميل الصورة بدون معالجة
            $file->storeAs($folder, $filename, $disk);
        }
        
        // إنشاء صورة مصغرة إذا تم طلب ذلك
        $thumbnailPath = null;
        if (isset($options['create_thumbnail']) && $options['create_thumbnail']) {
            $thumbnailWidth = $options['thumbnail_width'] ?? 200;
            $thumbnailHeight = $options['thumbnail_height'] ?? 200;
            $thumbnailFilename = 'thumb_' . $filename;
            $thumbnailPath = $folder . '/' . $thumbnailFilename;
            
            // إنشاء الصورة المصغرة
            $image = Image::make($file);
            $image->fit($thumbnailWidth, $thumbnailHeight);
            $fullThumbnailPath = Storage::disk($disk)->path($thumbnailPath);
            $image->save($fullThumbnailPath);
        }
        
        // الحصول على معلومات الصورة
        $imageInfo = [
            'name' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'folder' => $folder,
            'disk' => $disk,
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => self::TYPE_IMAGE,
            'url' => $public ? Storage::disk($disk)->url($path) : null,
        ];
        
        // إضافة معلومات الصورة المصغرة إذا تم إنشاؤها
        if ($thumbnailPath) {
            $imageInfo['thumbnail'] = [
                'name' => $thumbnailFilename,
                'path' => $thumbnailPath,
                'url' => $public ? Storage::disk($disk)->url($thumbnailPath) : null,
            ];
        }
        
        // تسجيل النشاط
        $this->logService->logActivity(
            "Uploaded image: {$imageInfo['original_name']}",
            'file',
            null,
            $imageInfo,
            LogService::ACTIVITY_TYPE_CREATE
        );
        
        return $imageInfo;
    }
    
    /**
     * تحميل ملفات متعددة
     *
     * @param array $files الملفات المراد تحميلها
     * @param string $folder المجلد المراد التخزين فيه
     * @param bool $public هل الملفات عامة أم خاصة
     * @param array $options خيارات إضافية
     * @return array معلومات الملفات المحملة
     */
    public function uploadMultipleFiles(array $files, string $folder, bool $public = true, array $options = []): array
    {
        $uploadedFiles = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                // تحديد ما إذا كان الملف صورة أم لا
                if ($this->isImage($file) && (!isset($options['process_images']) || $options['process_images'])) {
                    $uploadedFiles[] = $this->uploadImage($file, $folder, null, $public, $options);
                } else {
                    $uploadedFiles[] = $this->uploadFile($file, $folder, null, $public, $options);
                }
            }
        }
        
        return $uploadedFiles;
    }
    
    /**
     * حذف ملف
     *
     * @param string $path مسار الملف
     * @param string $disk القرص المخزن عليه الملف
     * @return bool نجاح العملية
     */
    public function deleteFile(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            // حذف الملف
            $result = Storage::disk($disk)->delete($path);
            
            // تسجيل النشاط
            $this->logService->logActivity(
                "Deleted file: {$path}",
                'file',
                null,
                ['path' => $path, 'disk' => $disk],
                LogService::ACTIVITY_TYPE_DELETE
            );
            
            return $result;
        }
        
        return false;
    }
    
    /**
     * حذف صورة مع الصورة المصغرة إذا وجدت
     *
     * @param string $path مسار الصورة
     * @param string $disk القرص المخزن عليه الملف
     * @return bool نجاح العملية
     */
    public function deleteImage(string $path, string $disk = 'public'): bool
    {
        $result = $this->deleteFile($path, $disk);
        
        // محاولة حذف الصورة المصغرة إذا وجدت
        $pathInfo = pathinfo($path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
        
        if (Storage::disk($disk)->exists($thumbnailPath)) {
            Storage::disk($disk)->delete($thumbnailPath);
        }
        
        return $result;
    }
    
    /**
     * حذف مجلد وجميع محتوياته
     *
     * @param string $folder مسار المجلد
     * @param string $disk القرص المخزن عليه المجلد
     * @return bool نجاح العملية
     */
    public function deleteFolder(string $folder, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($folder)) {
            // حذف المجلد وجميع محتوياته
            $result = Storage::disk($disk)->deleteDirectory($folder);
            
            // تسجيل النشاط
            $this->logService->logActivity(
                "Deleted folder: {$folder}",
                'folder',
                null,
                ['folder' => $folder, 'disk' => $disk],
                LogService::ACTIVITY_TYPE_DELETE
            );
            
            return $result;
        }
        
        return false;
    }
    
    /**
     * نسخ ملف
     *
     * @param string $sourcePath مسار الملف المصدر
     * @param string $destinationPath مسار الملف الوجهة
     * @param string $disk القرص المخزن عليه الملف
     * @return bool نجاح العملية
     */
    public function copyFile(string $sourcePath, string $destinationPath, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($sourcePath)) {
            // نسخ الملف
            $result = Storage::disk($disk)->copy($sourcePath, $destinationPath);
            
            // تسجيل النشاط
            $this->logService->logActivity(
                "Copied file from {$sourcePath} to {$destinationPath}",
                'file',
                null,
                ['source' => $sourcePath, 'destination' => $destinationPath, 'disk' => $disk],
                LogService::ACTIVITY_TYPE_OTHER
            );
            
            return $result;
        }
        
        return false;
    }
    
    /**
     * نقل ملف
     *
     * @param string $sourcePath مسار الملف المصدر
     * @param string $destinationPath مسار الملف الوجهة
     * @param string $disk القرص المخزن عليه الملف
     * @return bool نجاح العملية
     */
    public function moveFile(string $sourcePath, string $destinationPath, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($sourcePath)) {
            // نقل الملف
            $result = Storage::disk($disk)->move($sourcePath, $destinationPath);
            
            // تسجيل النشاط
            $this->logService->logActivity(
                "Moved file from {$sourcePath} to {$destinationPath}",
                'file',
                null,
                ['source' => $sourcePath, 'destination' => $destinationPath, 'disk' => $disk],
                LogService::ACTIVITY_TYPE_OTHER
            );
            
            return $result;
        }
        
        return false;
    }
    
    /**
     * إنشاء رابط تنزيل مؤقت للملف
     *
     * @param string $path مسار الملف
     * @param int $expirationMinutes مدة صلاحية الرابط بالدقائق
     * @param string $disk القرص المخزن عليه الملف
     * @return string|null رابط التنزيل
     */
    public function createTemporaryUrl(string $path, int $expirationMinutes = 60, string $disk = 'public'): ?string
    {
        if (Storage::disk($disk)->exists($path)) {
            // إنشاء رابط مؤقت
            $url = Storage::disk($disk)->temporaryUrl(
                $path,
                now()->addMinutes($expirationMinutes)
            );
            
            // تسجيل النشاط
            $this->logService->logActivity(
                "Created temporary URL for file: {$path}",
                'file',
                null,
                ['path' => $path, 'expiration_minutes' => $expirationMinutes, 'disk' => $disk],
                LogService::ACTIVITY_TYPE_OTHER
            );
            
            return $url;
        }
        
        return null;
    }
    
    /**
     * الحصول على محتوى ملف
     *
     * @param string $path مسار الملف
     * @param string $disk القرص المخزن عليه الملف
     * @return string|null محتوى الملف
     */
    public function getFileContents(string $path, string $disk = 'public'): ?string
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->get($path);
        }
        
        return null;
    }
    
    /**
     * كتابة محتوى إلى ملف
     *
     * @param string $path مسار الملف
     * @param string $contents محتوى الملف
     * @param string $disk القرص المخزن عليه الملف
     * @return bool نجاح العملية
     */
    public function putFileContents(string $path, string $contents, string $disk = 'public'): bool
    {
        $result = Storage::disk($disk)->put($path, $contents);
        
        // تسجيل النشاط
        $this->logService->logActivity(
            "Updated file contents: {$path}",
            'file',
            null,
            ['path' => $path, 'disk' => $disk, 'size' => strlen($contents)],
            LogService::ACTIVITY_TYPE_UPDATE
        );
        
        return $result;
    }
    
    /**
     * إنشاء مجلد
     *
     * @param string $path مسار المجلد
     * @param string $disk القرص المخزن عليه المجلد
     * @return bool نجاح العملية
     */
    public function createFolder(string $path, string $disk = 'public'): bool
    {
        $result = Storage::disk($disk)->makeDirectory($path);
        
        // تسجيل النشاط
        $this->logService->logActivity(
            "Created folder: {$path}",
            'folder',
            null,
            ['path' => $path, 'disk' => $disk],
            LogService::ACTIVITY_TYPE_CREATE
        );
        
        return $result;
    }
    
    /**
     * الحصول على قائمة الملفات في مجلد
     *
     * @param string $folder مسار المجلد
     * @param bool $recursive هل يتم البحث في المجلدات الفرعية
     * @param string $disk القرص المخزن عليه المجلد
     * @return array قائمة الملفات
     */
    public function getFiles(string $folder, bool $recursive = false, string $disk = 'public'): array
    {
        if ($recursive) {
            $files = Storage::disk($disk)->allFiles($folder);
        } else {
            $files = Storage::disk($disk)->files($folder);
        }
        
        $result = [];
        
        foreach ($files as $file) {
            $result[] = [
                'name' => basename($file),
                'path' => $file,
                'url' => Storage::disk($disk)->url($file),
                'size' => Storage::disk($disk)->size($file),
                'last_modified' => Storage::disk($disk)->lastModified($file),
                'extension' => pathinfo($file, PATHINFO_EXTENSION),
                'type' => $this->getFileTypeByExtension(pathinfo($file, PATHINFO_EXTENSION)),
            ];
        }
        
        return $result;
    }
    
    /**
     * الحصول على قائمة المجلدات في مجلد
     *
     * @param string $folder مسار المجلد
     * @param bool $recursive هل يتم البحث في المجلدات الفرعية
     * @param string $disk القرص المخزن عليه المجلد
     * @return array قائمة المجلدات
     */
    public function getFolders(string $folder, bool $recursive = false, string $disk = 'public'): array
    {
        if ($recursive) {
            $directories = Storage::disk($disk)->allDirectories($folder);
        } else {
            $directories = Storage::disk($disk)->directories($folder);
        }
        
        $result = [];
        
        foreach ($directories as $directory) {
            $result[] = [
                'name' => basename($directory),
                'path' => $directory,
                'last_modified' => Storage::disk($disk)->lastModified($directory),
            ];
        }
        
        return $result;
    }
    
    /**
     * تنظيف المجلد المؤقت
     *
     * @param int $olderThanMinutes حذف الملفات الأقدم من هذا العدد من الدقائق
     * @param string $disk القرص المخزن عليه المجلد
     * @return int عدد الملفات المحذوفة
     */
    public function cleanTempFolder(int $olderThanMinutes = 60, string $disk = 'public'): int
    {
        $files = Storage::disk($disk)->files(self::FOLDER_TEMP);
        $deletedCount = 0;
        
        $cutoffTime = now()->subMinutes($olderThanMinutes)->getTimestamp();
        
        foreach ($files as $file) {
            $lastModified = Storage::disk($disk)->lastModified($file);
            
            if ($lastModified < $cutoffTime) {
                if (Storage::disk($disk)->delete($file)) {
                    $deletedCount++;
                }
            }
        }
        
        // تسجيل النشاط
        $this->logService->logActivity(
            "Cleaned temp folder: {$deletedCount} files deleted",
            'folder',
            null,
            ['folder' => self::FOLDER_TEMP, 'deleted_count' => $deletedCount, 'older_than_minutes' => $olderThanMinutes],
            LogService::ACTIVITY_TYPE_OTHER
        );
        
        return $deletedCount;
    }
    
    /**
     * تحديد ما إذا كان الملف صورة
     *
     * @param UploadedFile $file الملف
     * @return bool هل الملف صورة
     */
    public function isImage(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        return strpos($mimeType, 'image/') === 0;
    }
    
    /**
     * الحصول على نوع الملف بناءً على الملف المرفوع
     *
     * @param UploadedFile $file الملف
     * @return string نوع الملف
     */
    public function getFileType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        
        // تحديد نوع الملف بناءً على نوع MIME
        if (strpos($mimeType, 'image/') === 0) {
            return self::TYPE_IMAGE;
        } elseif (strpos($mimeType, 'application/pdf') === 0) {
            return self::TYPE_PDF;
        } elseif (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.oasis.opendocument.text',
            'text/plain',
            'text/rtf',
        ])) {
            return self::TYPE_DOCUMENT;
        } elseif (in_array($mimeType, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.oasis.opendocument.spreadsheet',
            'text/csv',
        ])) {
            return self::TYPE_SPREADSHEET;
        } elseif (in_array($mimeType, [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/x-tar',
            'application/x-gzip',
        ]) || in_array($extension, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            return self::TYPE_ARCHIVE;
        } elseif (strpos($mimeType, 'audio/') === 0) {
            return self::TYPE_AUDIO;
        } elseif (strpos($mimeType, 'video/') === 0) {
            return self::TYPE_VIDEO;
        }
        
        return self::TYPE_OTHER;
    }
    
    /**
     * الحصول على نوع الملف بناءً على امتداده
     *
     * @param string $extension امتداد الملف
     * @return string نوع الملف
     */
    public function getFileTypeByExtension(string $extension): string
    {
        $extension = strtolower($extension);
        
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        $documentExtensions = ['doc', 'docx', 'odt', 'txt', 'rtf'];
        $spreadsheetExtensions = ['xls', 'xlsx', 'ods', 'csv'];
        $pdfExtensions = ['pdf'];
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'flac', 'aac'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
        
        if (in_array($extension, $imageExtensions)) {
            return self::TYPE_IMAGE;
        } elseif (in_array($extension, $documentExtensions)) {
            return self::TYPE_DOCUMENT;
        } elseif (in_array($extension, $spreadsheetExtensions)) {
            return self::TYPE_SPREADSHEET;
        } elseif (in_array($extension, $pdfExtensions)) {
            return self::TYPE_PDF;
        } elseif (in_array($extension, $archiveExtensions)) {
            return self::TYPE_ARCHIVE;
        } elseif (in_array($extension, $audioExtensions)) {
            return self::TYPE_AUDIO;
        } elseif (in_array($extension, $videoExtensions)) {
            return self::TYPE_VIDEO;
        }
        
        return self::TYPE_OTHER;
    }
    
    /**
     * الحصول على الحجم الأقصى للملف المسموح به
     *
     * @param string $type نوع الملف
     * @return int الحجم الأقصى بالبايت
     */
    public function getMaxFileSize(string $type = null): int
    {
        // الحجم الافتراضي: 10 ميجابايت
        $defaultSize = 10 * 1024 * 1024;
        
        if ($type === null) {
            return $defaultSize;
        }
        
        switch ($type) {
            case self::TYPE_IMAGE:
                return 5 * 1024 * 1024; // 5 ميجابايت
            case self::TYPE_DOCUMENT:
            case self::TYPE_PDF:
                return 20 * 1024 * 1024; // 20 ميجابايت
            case self::TYPE_SPREADSHEET:
                return 15 * 1024 * 1024; // 15 ميجابايت
            case self::TYPE_ARCHIVE:
                return 50 * 1024 * 1024; // 50 ميجابايت
            case self::TYPE_AUDIO:
                return 30 * 1024 * 1024; // 30 ميجابايت
            case self::TYPE_VIDEO:
                return 100 * 1024 * 1024; // 100 ميجابايت
            default:
                return $defaultSize;
        }
    }
    
    /**
     * الحصول على الامتدادات المسموح بها
     *
     * @param string $type نوع الملف
     * @return array الامتدادات المسموح بها
     */
    public function getAllowedExtensions(string $type = null): array
    {
        if ($type === null) {
            return array_merge(
                $this->getAllowedExtensions(self::TYPE_IMAGE),
                $this->getAllowedExtensions(self::TYPE_DOCUMENT),
                $this->getAllowedExtensions(self::TYPE_SPREADSHEET),
                $this->getAllowedExtensions(self::TYPE_PDF),
                $this->getAllowedExtensions(self::TYPE_ARCHIVE)
            );
        }
        
        switch ($type) {
            case self::TYPE_IMAGE:
                return ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
            case self::TYPE_DOCUMENT:
                return ['doc', 'docx', 'odt', 'txt', 'rtf'];
            case self::TYPE_SPREADSHEET:
                return ['xls', 'xlsx', 'ods', 'csv'];
            case self::TYPE_PDF:
                return ['pdf'];
            case self::TYPE_ARCHIVE:
                return ['zip', 'rar', '7z', 'tar', 'gz'];
            case self::TYPE_AUDIO:
                return ['mp3', 'wav', 'ogg', 'flac', 'aac'];
            case self::TYPE_VIDEO:
                return ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
            default:
                return [];
        }
    }
    
    /**
     * تحويل حجم الملف إلى صيغة مقروءة
     *
     * @param int $bytes حجم الملف بالبايت
     * @param int $precision عدد الأرقام العشرية
     * @return string الحجم بصيغة مقروءة
     */
    public function formatFileSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}