Webnotes
========

Webnotes is a simple, single-user, password-protected<sup>1</sup> php webapp for taking and saving notes.

1. Just because it's password-protected doesn't mean it's secure. This webapp is designed to store notes quickly on the go, not as a repository for secure information.

![screenshot](http://code.tdeitch.com/webnotes/screenshot.png)

Installation
------------
Open `index.php` and find the line that looks like this:

    $cmp_pass[] = hash('sha256', 'password');

Replace "password" with your password of choice. Upload `index.php`, `style.css`, and `.htaccess` to a folder on a server with PHP. `.htaccess` is a hidden file, so be certain it's uploaded to the server. Finally, change the permissions of the folder to 777.
