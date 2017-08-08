# srt-subtitle-file-fix-PHP
Simple PHP scripts to adjust the offset or fps of .SRT files 

# how to use
to add 13.3 seconds to the SRT file:
php SRT_add_remove_offset.php <SRT file> 13.3

to remove 5.3 seconds to the SRT file:
php SRT_add_remove_offset.php <SRT file> -5.3

to change the frame rate of the SRT file to 133%:
php SRT_change_to_rate.php <SRT file> 133
