# Manager report exports

Manager report pages expose PDF and Excel downloads through the report export routes. PDF files use the dedicated formal report template instead of printing the dashboard page.

After deploying these changes, run:

```bash
php artisan migrate
npm run build
php artisan test
```
