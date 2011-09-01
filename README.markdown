Webnotes
========

Webnotes is a simple, single-user, password-protected<sup>1</sup> php webapp for taking and saving notes.

1. Just because it's password-protected doesn't mean it's secure. This webapp is designed to store notes quickly on the go, not as a repository for secure information.

![screenshot](screenshot.png)

Installation
------------
Open `index.php` and find the line that looks like this:

    $cmp_pass[] = hash('sha256', 'password');

Replace "password" with your password of choice. Upload `index.php` and `style.css` to a folder on a server with PHP. Change the permissions of the folder to 777.
