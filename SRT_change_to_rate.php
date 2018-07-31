<?php


function send_console($level, $message)
{
    echo $level . ' - ' . $message . PHP_EOL;
}

//--------------------------------------------------------------

function identify_process_line($line, $new_fps)
{
    if (preg_match('/^(\s*)$/i', $line, $matches) === 1)
    {
        $result = 'TYPE3_EMPTYSPACE';
        $new_line = '';
    }
    elseif (preg_match('/^([0-9]*)$/i', $line, $matches) === 1)
    {
        $result = 'TYPE1_COUNTER';
        $new_line = $line;
    }
    elseif (preg_match('/^(([0-9]{2}):([0-9]{2}):([0-9]{2}),([0-9]{3})([\s]*)(-->)([\s]*)([0-9]{2}):([0-9]{2}):([0-9]{2}),([0-9]{3}))$/i', $line, $matches) === 1)
    {
        $result = 'TYPE2_TIMES';
        // conversion start
        $start_time = $matches[2]*3600 + $matches[3]*60 + $matches[4] + $matches[5]/1000;
        $end_time   = $matches[9]*3600 + $matches[10]*60 + $matches[11] + $matches[12]/1000;
        $start_time = $start_time * ($new_fps/100);
        $end_time   = $end_time * ($new_fps/100);
        $start_milli = substr(strval(round($start_time - intval($start_time), 3)), 2); // 0.456 -> string -> substring
        $end_milli = substr(strval(round($end_time - intval($end_time), 3)), 2);
        // conversion end

        // $new_line = gmdate("H:i:s", $start_time) . ',000 --> ' . gmdate("H:i:s", $end_time) . ',000'; // old way
        $new_line = gmdate("H:i:s", $start_time) . ',' . $start_milli . ' --> ' . gmdate("H:i:s", $end_time) . ',' . $end_milli;
    }
    else
    {
        $result = 'TYPE4_SUBTITLES';
        $new_line = $line;
    }
    //
    return $new_line;
}

//--------------------------------------------------------------

function do_conversion_main($file_name, $new_fps)
{
    $srt_contents = @file_get_contents($file_name);
    if ($srt_contents === FALSE)
    {
        send_console('ERROR', 'SRT file: ' . $file_name . ' was NOT found.');
        exit;
    }
    // move it to .orig:
    rename($file_name, $file_name . '.orig');

    $processed_srt_contents = '';

    foreach(preg_split("/((\r?\n)|(\r\n?))/", $srt_contents) as $line)
    {
        // process each $line:
        // 
        $new_line = identify_process_line($line, $new_fps);
        $processed_srt_contents .= $new_line . PHP_EOL;
    }
    // change EOL to windows, by https://stackoverflow.com/questions/7836632/how-to-replace-different-newline-styles-in-php-the-smartest-way:
    $processed_srt_contents = preg_replace('~(*BSR_ANYCRLF)\R~', "\r\n", $processed_srt_contents);
    //
    if ( @file_put_contents($file_name, $processed_srt_contents) === FALSE)
    {
        send_console('ERROR', 'SRT file: ' . $file_name . ' could NOT be written.');
        exit;
    }
    //
    return TRUE;
}

//--------------------------------------------------------------
//--------------------------------------------------------------
//--------------------------------------------------------------

// main below:

if (isset($argv[1]))
{
    $file_name = $argv[1];
}
else
{
    send_console('ERROR', 'No SRT file was provided.');
    exit;
}

//

if (isset($argv[2]))
{
    if (is_numeric($argv[2]))
    {
        if ($argv[2] >= 100)
        {
            $new_fps = $argv[2];
        }
        else
        {
            send_console('ERROR', 'FPS rate provided should be greater than 100.');
            send_console('INFO', 'USAGE: <script> <SRT file to work on> <new FPS rate at 100base>');
            exit;
        }
    }
    else
    {
        send_console('ERROR', 'No numeric FPS provided.');
        send_console('INFO', 'USAGE: <script> <SRT file to work on> <new FPS rate at 100base>');
        exit;
    }
}
else
{
    send_console('ERROR', 'No FPS provided.');
    send_console('INFO', 'USAGE: <script> <SRT file to work on> <new FPS rate at 100base>');
    exit;
}

do_conversion_main($file_name, $new_fps);
