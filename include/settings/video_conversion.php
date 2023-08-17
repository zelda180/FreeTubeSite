<?php
# Convert video to mp4
$cmd_mp4 = "nohup $config[ffmpeg] -y -i '$video_src' -crf 22.0 -c:v libx264 -c:a aac -ar 48000 -b:a 160k -coder 1 -threads 0 $video_flv > $ffmpeg_out 2>&1 &";
# Witch ffmpeg command to use for what video extention
$convert_mp4 = $cmd_mp4;
$convert_3gp = $cmd_mp4;
$convert_mov = $cmd_mp4;
$convert_asf = $cmd_mp4;
$convert_mpg = $cmd_mp4;
$convert_avi = $cmd_mp4;
$convert_mpeg = $cmd_mp4;
$convert_wmv = $cmd_mp4;
$convert_rm = $cmd_mp4;
$convert_dat = $cmd_mp4;
$convert_m4v = $cmd_mp4;
$convert_webm = $cmd_mp4;
$convert_flv = $cmd_mp4;
