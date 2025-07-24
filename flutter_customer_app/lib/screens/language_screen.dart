import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../services/language_service.dart';
import '../utils/app_extensions.dart';

class LanguageScreen extends StatelessWidget {
  const LanguageScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final languageService = Provider.of<LanguageService>(context);
    
    return Scaffold(
      appBar: AppBar(
        title: Text('app.language'.tr(context)),
      ),
      body: ListView(
        children: [
          _buildLanguageItem(
            context,
            'العربية',
            'ar',
            languageService,
            const AssetImage('assets/images/ar_flag.png'),
          ),
          _buildLanguageItem(
            context,
            'English',
            'en',
            languageService,
            const AssetImage('assets/images/en_flag.png'),
          ),
        ],
      ),
    );
  }

  Widget _buildLanguageItem(
    BuildContext context,
    String title,
    String languageCode,
    LanguageService languageService,
    ImageProvider? flagImage,
  ) {
    final isSelected = languageService.currentLocale.languageCode == languageCode;
    
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      elevation: isSelected ? 4 : 1,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(
          color: isSelected ? Theme.of(context).primaryColor : Colors.transparent,
          width: 2,
        ),
      ),
      child: InkWell(
        onTap: () {
          languageService.changeLanguage(languageCode);
        },
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              if (flagImage != null) ...[
                CircleAvatar(
                  backgroundImage: flagImage,
                  radius: 20,
                ),
                const SizedBox(width: 16),
              ],
              Expanded(
                child: Text(
                  title,
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                  ),
                ),
              ),
              if (isSelected)
                Icon(
                  Icons.check_circle,
                  color: Theme.of(context).primaryColor,
                ),
            ],
          ),
        ),
      ),
    );
  }
} 