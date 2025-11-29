# Support Page QR Code Setup

## Overview
The support page now features a two-column layout with:
- Left column: Project information and descriptions
- Right column: QR code with parallax scroll effect and PayPal button placeholder

## QR Code Image Setup

### 1. Save the QR Code Image
Save the Napas QR code image to:
```
public/images/napas-qr.jpg
```

The image path is already configured in the blade template:
```blade
<img src="{{ asset('images/napas-qr.jpg') }}" alt="Napas QR Code">
```

### 2. Image Requirements
- Format: JPG, PNG, or WebP
- Recommended size: 800x800px or larger for best quality
- The image will be responsive and scale to fit the container

### 3. Directory Structure
```
public/
  └── images/
      └── napas-qr.jpg  <- Save QR code here
```

## Features Implemented

### Parallax Scroll Effect
The QR code container moves vertically as you scroll, creating a subtle parallax effect:
- Scroll down: image moves down slightly
- Scroll up: image moves up slightly
- Multiplier: 0.2x scroll speed

### Sticky Positioning
On large screens (lg breakpoint), the QR code card sticks to the top of the viewport with:
- `position: sticky`
- `top: 24px (6rem)`

### PayPal Button Placeholder
A placeholder button is ready for PayPal integration:
- Yellow PayPal-styled button
- Container ID: `paypal-button-container`
- Currently shows "Coming Soon" message

## Translations Added

### English (`resources/lang/en/nav.php`)
```php
'support_methods' => 'Payment Methods',
'donation_help_text' => 'Your support helps us continue developing this free language learning platform. Every contribution makes a difference!',
```

### Vietnamese (`resources/lang/vi/nav.php`)
```php
'support_methods' => 'Phương Thức Thanh Toán',
'donation_help_text' => 'Sự hỗ trợ của bạn giúp chúng tôi tiếp tục phát triển nền tảng học ngôn ngữ miễn phí này. Mọi đóng góp đều tạo nên sự khác biệt!',
```

## Future PayPal Integration

To integrate PayPal later, replace the placeholder button with PayPal SDK:

```html
<!-- Add to layout head -->
<script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&currency=USD"></script>

<!-- Replace placeholder with PayPal render -->
<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '5.00' // or dynamic amount
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Handle successful payment
        });
    }
}).render('#paypal-button-container');
</script>
```

## Testing

1. Navigate to `/support` page
2. Verify QR code displays correctly
3. Test scroll parallax effect on desktop
4. Test responsive layout on mobile devices
5. Check both English and Vietnamese translations

## Technical Details

- File: `resources/views/support.blade.php`
- Layout: 12-column grid (7 cols text, 5 cols QR)
- Alpine.js for parallax effect
- TailwindCSS for styling
- Responsive breakpoint: `lg` (1024px)
