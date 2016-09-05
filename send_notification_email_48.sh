#!/bin/bash
gtime=`date +%H:%M`
gdate=` date +%d/%m/%Y -d "+1 day"`
echo $gtime
echo $gdate
user=root
pass=QQcqxLNdL0H3
dbname=joomla
mail_text=/home/webmaster/www/staging.liverpoolfc.ru/htdocs/modules/mod_contest/email_text
game_id=$(echo "select c_id from jos_calendar  where c_lasku is null and c_date > date(now()) order by c_date asc limit 1;"|mysql -u"$user" -p"$pass" --skip-column-names $dbname);
script=/home/webmaster/www/staging.liverpoolfc.ru/htdocs/modules/mod_contest/send_notification_email_24.sh
team=$(echo "select t_name_eng from jos_teams where t_id in (select t_id from jos_calendar where c_id = $game_id);"|mysql -u"$user" -p"$pass" --skip-column-names $dbname)
link=http://www.liverpoolfc.ru/konkurs-prognozov-lfk-ru
at -f $script now + 24 hour
cat /dev/null>$mail_text
echo "From: forum@liverpoolfc.ru
Subject: Конкурс прогнозов Liverpool FC

Близится матч против $team. Не забудь подать свой прогноз на матч в конкурсе прогнозов на ЛФК.ру. Прогнозы принимаются до $gtime (по Лондону) $gdate.
$link
Удачи!">$mail_text
query="SELECT email FROM jos_users WHERE id IN ( SELECT user_id FROM frcst_player WHERE contest_id = 2 AND user_id NOT IN ( SELECT user_id FROM frcst_forecast WHERE game_id = $game_id ))"
for email in $(echo $query|mysql -u"$user" -p"$pass" --skip-column-names $dbname);
do
        ssmtp $email < $mail_text;
done
