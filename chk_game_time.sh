#!/bin/bash
user=root
pass=QQcqxLNdL0H3
dbname=joomla
script=/home/webmaster/www/staging.liverpoolfc.ru/htdocs/modules/mod_contest/send_notification_email_48.sh
gamedatetime=$(echo "SELECT concat( SUBSTR(CONVERT(c_time, CHAR), 1, 5), ' ', CONVERT ( ADDDATE(c_date, INTERVAL - 2 DAY), CHAR )) game_time FROM jos_calendar WHERE c_lasku IS NULL 
AND c_date > date(now()) ORDER BY c_date ASC LIMIT 1;"|mysql -u$user -p$pass --skip-column-names $dbname)
at -f $script $gamedatetime
