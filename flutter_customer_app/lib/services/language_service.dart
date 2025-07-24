import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:provider/provider.dart';

class LanguageService extends ChangeNotifier {
  Locale _currentLocale = const Locale('ar');
  bool _isLoading = false;

  Locale get currentLocale => _currentLocale;
  bool get isLoading => _isLoading;
  bool get isRtl => _currentLocale.languageCode == 'ar';

  LanguageService() {
    loadSavedLanguage();
  }

  Future<void> loadSavedLanguage() async {
    _isLoading = true;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final languageCode = prefs.getString('language_code');
      
      if (languageCode != null) {
        _currentLocale = Locale(languageCode);
      }
    } catch (e) {
      // If there's an error, use default locale
      _currentLocale = const Locale('ar');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> changeLanguage(String languageCode) async {
    if (languageCode != _currentLocale.languageCode) {
      _isLoading = true;
      notifyListeners();

      try {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('language_code', languageCode);
        _currentLocale = Locale(languageCode);
      } catch (e) {
        // Handle error
      } finally {
        _isLoading = false;
        notifyListeners();
      }
    }
  }

  static LanguageService of(BuildContext context) {
    return Provider.of<LanguageService>(context, listen: false);
  }
} 