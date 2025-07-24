# E-Commerce Customer App

A Flutter mobile application for customers to browse products, manage shopping cart, and place orders.

## Features

- User authentication (login, register, profile management)
- Product browsing and searching
- Category filtering
- Product details with images, description, and related products
- Shopping cart management
- Checkout process
- Order tracking

## Project Structure

```
lib/
├── main.dart              # App entry point
├── models/               # Data models
│   ├── cart_item.dart
│   ├── category.dart
│   ├── product.dart
│   ├── product_price.dart
│   ├── product_stock.dart
│   ├── user.dart
│   └── warehouse.dart
├── screens/              # App screens
│   ├── auth/             # Authentication screens
│   │   ├── login_screen.dart
│   │   └── register_screen.dart
│   ├── cart_screen.dart
│   ├── checkout_screen.dart
│   ├── home_screen.dart
│   ├── order_success_screen.dart
│   ├── product_detail_screen.dart
│   └── profile_screen.dart
├── services/             # API and business logic services
│   ├── api_service.dart
│   ├── auth_service.dart
│   ├── cart_service.dart
│   └── product_service.dart
├── utils/               # Utilities and helpers
│   └── app_theme.dart
└── widgets/             # Reusable UI components
    ├── cart_item_tile.dart
    ├── category_chip.dart
    └── product_card.dart
```

## Getting Started

### Prerequisites

- Flutter SDK (version 3.0.0 or higher)
- Dart SDK (version 3.0.0 or higher)
- Android Studio / Xcode for mobile development

### Installation

1. Clone the repository
2. Navigate to the project directory
3. Install dependencies:
   ```bash
   flutter pub get
   ```
4. Create a `.env` file in the root directory with the following content:
   ```
   API_URL=http://your-api-url/api
   APP_NAME=E-Commerce App
   ```
5. Run the app:
   ```bash
   flutter run
   ```

## API Integration

This app connects to a Laravel backend API. Make sure the API server is running and accessible. Update the API_URL in the `.env` file to point to your backend API.

## Dependencies

- flutter: The Flutter SDK
- provider: State management
- http: API requests
- shared_preferences: Local storage
- flutter_secure_storage: Secure storage for tokens
- cached_network_image: Image caching
- carousel_slider: Image carousel
- flutter_svg: SVG support
- shimmer: Loading effects
- intl: Internationalization
- url_launcher: Opening URLs
- flutter_rating_bar: Rating UI
- badges: Badge UI
- flutter_dotenv: Environment variables 