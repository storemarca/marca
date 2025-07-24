<?php

namespace App\Services;

use App\Models\User;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\ProductStock;
use App\Notifications\OrderCreated;
use App\Notifications\OrderStatusChanged;
use App\Notifications\PaymentReceived;
use App\Notifications\ShipmentCreated;
use App\Notifications\ReturnRequestCreated;
use App\Notifications\ReturnRequestStatusChanged;
use App\Notifications\LowStockAlert;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Exception;

class NotificationService
{
    /**
     * Send notification for new order
     *
     * @param Order $order
     * @return bool
     */
    public function sendOrderCreatedNotification(Order $order): bool
    {
        try {
            // Notify customer
            $order->customer->notify(new OrderCreated($order));
            
            // Notify admin users
            $adminUsers = User::role(['admin', 'sales_manager'])->get();
            Notification::send($adminUsers, new OrderCreated($order));
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send order created notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification for order status change
     *
     * @param Order $order
     * @param string $oldStatus
     * @return bool
     */
    public function sendOrderStatusChangedNotification(Order $order, string $oldStatus): bool
    {
        try {
            // Notify customer
            $order->customer->notify(new OrderStatusChanged($order, $oldStatus));
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send order status changed notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification for payment received
     *
     * @param Order $order
     * @param float $amount
     * @return bool
     */
    public function sendPaymentReceivedNotification(Order $order, float $amount): bool
    {
        try {
            // Notify customer
            $order->customer->notify(new PaymentReceived($order, $amount));
            
            // Notify admin users
            $adminUsers = User::role(['admin', 'finance_manager'])->get();
            Notification::send($adminUsers, new PaymentReceived($order, $amount));
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send payment received notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification for shipment created
     *
     * @param Order $order
     * @param mixed $shipment
     * @return bool
     */
    public function sendShipmentCreatedNotification(Order $order, $shipment): bool
    {
        try {
            // Notify customer
            $order->customer->notify(new ShipmentCreated($order, $shipment));
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send shipment created notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification for return request created
     *
     * @param ReturnRequest $returnRequest
     * @return bool
     */
    public function sendReturnRequestCreatedNotification(ReturnRequest $returnRequest): bool
    {
        try {
            // Notify admin users
            $adminUsers = User::role(['admin', 'customer_service'])->get();
            Notification::send($adminUsers, new ReturnRequestCreated($returnRequest));
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send return request created notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification for return request status change
     *
     * @param ReturnRequest $returnRequest
     * @param string $oldStatus
     * @return bool
     */
    public function sendReturnRequestStatusChangedNotification(ReturnRequest $returnRequest, string $oldStatus): bool
    {
        try {
            // Notify customer
            $returnRequest->customer->notify(new ReturnRequestStatusChanged($returnRequest, $oldStatus));
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send return request status changed notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification for low stock
     *
     * @param ProductStock $productStock
     * @return bool
     */
    public function sendLowStockNotification(ProductStock $productStock): bool
    {
        try {
            // Notify warehouse managers and admins
            $adminUsers = User::role(['admin', 'warehouse_manager'])->get();
            Notification::send($adminUsers, new LowStockAlert($productStock));
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send low stock notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send custom notification to customer
     *
     * @param Customer $customer
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function sendCustomerNotification(Customer $customer, string $subject, string $message): bool
    {
        try {
            // Implementation depends on the notification system
            // This could send an email, SMS, or in-app notification
            
            // Example: Send email notification
            // Mail::to($customer->email)->send(new CustomNotification($subject, $message));
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send custom customer notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send bulk notification to multiple customers
     *
     * @param array $customerIds
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function sendBulkCustomerNotification(array $customerIds, string $subject, string $message): bool
    {
        try {
            $customers = Customer::whereIn('id', $customerIds)->get();
            
            foreach ($customers as $customer) {
                $this->sendCustomerNotification($customer, $subject, $message);
            }
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send bulk customer notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send notification to admin users with specific roles
     *
     * @param array $roles
     * @param string $subject
     * @param string $message
     * @return bool
     */
    public function sendAdminNotification(array $roles, string $subject, string $message): bool
    {
        try {
            $adminUsers = User::role($roles)->get();
            
            // Implementation depends on the notification system
            // This could send an email, SMS, or in-app notification
            
            // Example: Send email notification
            // Notification::send($adminUsers, new CustomAdminNotification($subject, $message));
            
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send admin notification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark all notifications as read for a user
     *
     * @param User $user
     * @return bool
     */
    public function markAllNotificationsAsRead(User $user): bool
    {
        try {
            $user->unreadNotifications->markAsRead();
            return true;
        } catch (Exception $e) {
            Log::error('Failed to mark notifications as read: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark a specific notification as read
     *
     * @param User $user
     * @param string $notificationId
     * @return bool
     */
    public function markNotificationAsRead(User $user, string $notificationId): bool
    {
        try {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            
            if ($notification) {
                $notification->markAsRead();
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a notification
     *
     * @param User $user
     * @param string $notificationId
     * @return bool
     */
    public function deleteNotification(User $user, string $notificationId): bool
    {
        try {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            
            if ($notification) {
                $notification->delete();
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('Failed to delete notification: ' . $e->getMessage());
            return false;
        }
    }
}