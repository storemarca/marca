import 'package:flutter/material.dart';
import 'app_localizations.dart';

extension StringExtension on String {
  String tr(BuildContext context) {
    return AppLocalizations.of(context).translate(this);
  }
}

extension BuildContextExtension on BuildContext {
  AppLocalizations get tr => AppLocalizations.of(this);
  bool get isRtl => AppLocalizations.of(this).isRtl;
  TextDirection get textDirection => isRtl ? TextDirection.rtl : TextDirection.ltr;
}

extension WidgetExtension on Widget {
  Widget directionality(BuildContext context) {
    return Directionality(
      textDirection: context.textDirection,
      child: this,
    );
  }
} 