## srt-subtitle-file-fix-PHP
Simple PHP scripts to adjust the offset or fps of .srt files.

## How to use
To add 13.3 seconds to the .srt file:
```php
php SRT_add_remove_offset.php <SRT file> 13.3
```

To remove 5.3 seconds to the .srt file:

```php
php SRT_add_remove_offset.php <SRT file> -5.3
```

To change the frame rate of the .srt file to 133%:

```php
php SRT_change_to_rate.php <SRT file> 133
```
