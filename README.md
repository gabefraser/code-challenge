# Code Challenge

## Why?
Inside this repository, you'll find my solution that'll take a appropriately created CSV file of transaction records. I have chosen to build this solution in the micro-framework 'Lumen', which is lightweight variant of 'Laravel', as it demonstrates my skills as a Full-Stack Web Developer and it allows me to speed up development without needing to custom code a backend handler.

## Files to look out for
[app\Http\Controllers\CSVController.php](https://github.com/gabefraser/code-challenge/blob/master/app/Http/Controllers/CSVController.php) - This is where the view rendering / CSV parsing occurs.

[app\Helpers\CSVParsingHelper.php](https://github.com/gabefraser/code-challenge/blob/master/app/Helpers/CSVParsingHelper.php) - This is where the translated from Pseudo code to PHP algorithm lives. 

[resources\views\home.blade.php](https://github.com/gabefraser/code-challenge/blob/master/resources/views/home.blade.php) - This is the Eloquent ORM migration schema resides.

[resources\views\layout.blade.php](https://github.com/gabefraser/code-challenge/blob/master/resources/views/layout.blade.php) - This is the root file that controls the `<html>`, `<head>` and `<body>` tags.

[resources\views\home.blade.php](https://github.com/gabefraser/code-challenge/blob/master/resources/views/home.blade.php) - This is where the table is displayed on the front-end.

## How to view
I have gone ahead and published this live on my site, [challenge.gabe.gg](https://challenge.gabe.gg) for your convenience. The database resets itself every 5 minutes.

If you'd like to view locally, you're more than welcome to spin up a local PHP server by running:

```bash
php artisan serve --port=8080
```