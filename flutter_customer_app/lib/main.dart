import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:provider/provider.dart';
import 'screens/splash_screen.dart';
import 'screens/home_screen.dart';
import 'screens/product_detail_screen.dart';
import 'screens/cart_screen.dart';
import 'screens/checkout_screen.dart';
import 'screens/order_success_screen.dart';
import 'screens/profile_screen.dart';
import 'screens/order_history_screen.dart';
import 'screens/order_tracking_screen.dart';
import 'screens/address_list_screen.dart';
import 'screens/address_form_screen.dart';
import 'screens/loyalty_points_screen.dart';
import 'screens/rewards_screen.dart';
import 'screens/affiliate_links_screen.dart';
import 'screens/affiliate_stats_screen.dart';
import 'screens/affiliate_earnings_screen.dart';
import 'screens/limited_offers_screen.dart';
import 'screens/language_screen.dart';
import 'screens/auth/login_screen.dart';
import 'screens/auth/register_screen.dart';
import 'services/auth_service.dart';
import 'services/cart_service.dart';
import 'services/product_service.dart';
import 'services/order_service.dart';
import 'services/address_service.dart';
import 'services/loyalty_service.dart';
import 'services/affiliate_service.dart';
import 'services/limited_offer_service.dart';
import 'services/shipping_service.dart';
import 'services/language_service.dart';
import 'utils/app_theme.dart';
import 'utils/app_localizations.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await dotenv.load(fileName: ".env");
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthService()),
        ChangeNotifierProvider(create: (_) => CartService()),
        ChangeNotifierProvider(create: (_) => ProductService()),
        ChangeNotifierProvider(create: (_) => OrderService()),
        ChangeNotifierProvider(create: (_) => AddressService()),
        ChangeNotifierProvider(create: (_) => LoyaltyService()),
        ChangeNotifierProvider(create: (_) => AffiliateService()),
        ChangeNotifierProvider(create: (_) => LimitedOfferService()),
        ChangeNotifierProvider(create: (_) => ShippingService()),
        ChangeNotifierProvider(create: (_) => LanguageService()),
      ],
      child: Consumer<LanguageService>(
        builder: (context, languageService, child) {
          return MaterialApp(
            title: dotenv.env['APP_NAME'] ?? 'E-Commerce App',
            debugShowCheckedModeBanner: false,
            theme: AppTheme.lightTheme,
            locale: languageService.currentLocale,
            supportedLocales: AppLocalizations.supportedLocales,
            localizationsDelegates: AppLocalizations.localizationsDelegates,
            localeResolutionCallback: AppLocalizations.localeResolutionCallback,
            initialRoute: '/',
            routes: {
              '/': (context) => const SplashScreen(),
              '/home': (context) => const HomeScreen(),
              '/product': (context) {
                final args = ModalRoute.of(context)?.settings.arguments
                    as Map<String, dynamic>?;
                return ProductDetailScreen(
                    productSlug: args?['productSlug'] ?? '');
              },
              '/cart': (context) => const CartScreen(),
              '/checkout': (context) => const CheckoutScreen(),
              '/order-success': (context) => const OrderSuccessScreen(),
              '/profile': (context) => const ProfileScreen(),
              '/orders': (context) => const OrderHistoryScreen(),
              '/order-tracking': (context) => const OrderTrackingScreen(),
              '/addresses': (context) => const AddressListScreen(),
              '/address-form': (context) => const AddressFormScreen(),
              '/login': (context) => const LoginScreen(),
              '/register': (context) => const RegisterScreen(),
              '/loyalty-points': (context) => const LoyaltyPointsScreen(),
              '/rewards': (context) => const RewardsScreen(),
              '/affiliate-links': (context) => const AffiliateLinksScreen(),
              '/affiliate-stats': (context) => const AffiliateStatsScreen(),
              '/affiliate-earnings': (context) =>
                  const AffiliateEarningsScreen(),
              '/limited-offers': (context) => const LimitedOffersScreen(),
              '/language': (context) => const LanguageScreen(),
            },
          );
        },
      ),
    );
  }
}
