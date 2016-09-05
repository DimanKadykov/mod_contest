<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php

    function renderForecastSelect($game, $forecasts, $homeOrAway, $selectNumber)
    {
        $forecasted = null;
        if (isset($forecasts[$game['c_id']])) {


            if ($homeOrAway == 'H') {
                if ($selectNumber == 'fS'){
                    $forecasted=$forecasts[$game['c_id']]['lfc_goals'];
                    $html='<option disabled selected value="'.$forecasted.'">'.$forecasted.'</option>';
                }
                else {
                    $forecasted = $forecasts[$game['c_id']]['opp_team_goals'];
                    $html='<option disabled selected value="'.$forecasted.'">'.$forecasted.'</option>';
                }

            } elseif ($homeOrAway == 'A') {
                if ($selectNumber == 'fS'){
                    $forecasted = $forecasts[$game['c_id']]['opp_team_goals'];
                    $html='<option disabled selected value="'.$forecasted.'">'.$forecasted.'</option>';
                }
                else {
                    $forecasted = $forecasts[$game['c_id']]['lfc_goals'];
                    $html='<option disabled selected value="'.$forecasted.'">'.$forecasted.'</option>';
                }
            }
        }
        else {
            $html='<option disabled selected value></option>';
        }
        foreach (range(0,9) as $digit) {
            $html .= '<option value="'.$digit.'">'.$digit.'</option>';
        }
        return $html;
    }
?>
<head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="/modules/mod_contest/js/js_scripts.js"></script>
</head>
<br>
<body id="forecastBody">
<style>
    .forecast-form ul {
        border: 1px solid #eee;
        float: left;
    }
    .forecast-form ul li {
        float: left;
        list-style: none;
        text-align: center;
        font-size: 100%;
        position: relative;
    }
    .forecast-season-form ul {
        border: 1px solid #eee;
        padding: 5px 0;
    }
    .forecast-season-form ul li {
        float: left;
        list-style: none;
        text-align: center;
        font-size: 100%;
    }
    .cf::after, .cf::before {
        clear: both;
        content: ' ';
        display: table;
    }
    ul.forecast-tab {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #000000;
    }
    ul.forecast-tab li {float: left;}
    ul.forecast-tab li a {
        display: inline-block;
        color: white;
        text-align: center;
        padding: 5px 10px;
        text-decoration: none;
        transition: 0.3s;
        font-size: 17px;

    }
    ul.forecast-tab li a:hover {background-color:  #4d4d4d;}
    ul.forecast-tab li a:focus,.active {background-color: #cc0000;}
    .content_block {
        border: 1px solid #ccc;
        border-top: none;
        width: 100%;
    }

</style>
<ul class="forecast-tab">
    <li><a href="/chants/konkurs-prognozov-lfk-ru#" class="top_tab" div_id="season_questions" onclick="return openForecast(event, 'season_questions')">Вопросы на сезон</a></li>
    <li><a href="/chants/konkurs-prognozov-lfk-ru#" class="top_tab active" div_id="games_list" onclick="return openForecast(event, 'games_list')">Прогнозы на матчи сезона</a><li>
    <li><a href="/chants/konkurs-prognozov-lfk-ru#" class="top_tab" div_id="results" onclick="return openForecast(event, 'results')">Результаты</a></li>
</ul>
<div class="content_block" id="season_questions" style="display: none">
    <?php date_default_timezone_set('Europe/London');$current_ts = strtotime(date("d-m-Y H:i:s", time()+3600)); $deadline =strtotime($games[1]['c_date'] . ' ' . $games[1]['c_time']); ?>
    <?php  if(isset($seasonForecasts['id']) or ($current_ts>$deadline)): ?>
        <form>
            <table id="new-table-season" width="100%" border="0" cellspacing="0" cellpadding="5"  style="border:1px solid #979595; font-size: 100%;"  class="new-table-season">
                <tr>
                    <th>Вопросы на сезон</th>
                    <th>Ваш прогноз</th>
                </tr>
                <tr style="background:#D7C9C9">
                    <td>Какое место займёт Ливерпуль в АПЛ по итогам сезона 2016/17?</td>
                    <td><?echo $seasonForecasts['lfc_place']?></td>
                </tr>
                <tr>
                    <td>Сколько голов забьёт Ливерпуль во всех играх в сезоне 2016/17</td>
                    <td><?echo $seasonForecasts['lfc_all_goals']?></td>
                </tr>
                <tr style="background:#D7C9C9">
                    <td>Сколько "сухих" матчей будет у Ливерпуля в сезоне 2016/17?</td>
                    <td><?echo $seasonForecasts['lfc_games_at_null']?></td>
                </tr>
                <tr>
                    <td>Кто будет лучшим бомбардиром по итогам сезона 2016/17?</td>
                    <td><?echo $seasonForecasts['lfc_best_goalscorer']?></td>
                </tr>
                <tr style="background:#D7C9C9">
                    <td>До какой стадии дойдет Ливерпуль в Кубке Лиги?</td>
                    <td><?echo $seasonForecasts['ligue_cup_stage']?></td>
                </tr>
                <tr>
                    <td>До какой стадии дойдет Ливерпуль в Кубке Англии?</td>
                    <td><?echo $seasonForecasts['fa_cup_stage']?></td>
                </tr>
                <tr style="background:#D7C9C9">
                    <td>Сколько голов забьёт Старридж во всех играх сезона 2016/17?</td>
                    <td><?echo $seasonForecasts['sturridge_goals']?></td>
                </tr>
            </table>
        </form>
    <?php else: ?>
    <form method="post" class="forecast-season-form">
        <input type="hidden" name="contest_id" value="<?= $contest->getId()?>">
        <input type="hidden" name="user_id" value="<?= $user->id?>">
            <ul class="cf">
            <li style="width: 68%">Какое место займёт Ливерпуль в АПЛ по итогам сезона 2016/17?</li>
            <li style="width: 7%">
                <select name="1" onchange="getSeasonSelectData(this, this.name)">
                    <option disabled selected value>-сделать прогноз-</option>
                    <option value="1 место">1 место</option>
                    <option value="2 место">2 место</option>
                    <option value="3 место">3 место</option>
                    <option value="4 место">4 место</option>
                    <option value="5 место">5 место</option>
                    <option value="6 место">6 место</option>
                    <option value="7 место">7 место</option>
                    <option value="8 место">8 место</option>
                    <option value="9 место">9 место</option>
                    <option value="10 место и ниже">10 место и ниже</option>
                </select>
            </li>
        </ul>
            <ul class="cf">
            <li style="width: 68%">Сколько голов забьёт Ливерпуль во всех играх в сезоне 2016/17?</li>
            <li style="width: 7%">
                <select name="2" onchange="getSeasonSelectData(this, this.name)">
                    <option disabled selected value>-сделать прогноз-</option>
                    <option value="меньше 50">меньше 50</option>
                    <option value="50-55">50-55</option>
                    <option value="56-60">56-60</option>
                    <option value="61-65">61-65</option>
                    <option value="66-70">66-70</option>
                    <option value="71-75">71-75</option>
                    <option value="76-80">76-80</option>
                    <option value="81-85">81-85</option>
                    <option value="86-90">86-90</option>
                    <option value="91-95">91-95</option>
                    <option value="96-100">96-100</option>
                    <option value="101-105">101-105</option>
                    <option value="106-111">106-111</option>
                    <option value="больше 111">больше 111</option>
                </select>
            </li>
        </ul>
            <ul class="cf">
            <li style="width: 68%">Сколько "сухих" матчей будет у Ливерпуля в сезоне 2016/17?	</li>
            <li style="width: 7%">
                <select name="3" onchange="getSeasonSelectData(this, this.name)">
                    <option disabled selected value>-сделать прогноз-</option>
                    <option value="меньше 10">меньше 10</option>
                    <option value="10-12">10-12</option>
                    <option value="13-15">13-15</option>
                    <option value="16-19">16-19</option>
                    <option value="20-22">20-22</option>
                    <option value="23-25">23-25</option>
                    <option value="26-28">26-28</option>
                    <option value="29-31">29-31</option>
                    <option value="больше 31">больше 31</option>
                </select>
            </li>
        </ul>
            <ul class="cf">
            <li style="width: 68%">Кто будет лучшим бомбардиром по итогам сезона 2016/17?</li>
            <li style="width: 7%">
                <select name="4" onchange="getSeasonSelectData(this, this.name)">
                    <option disabled selected value>-сделать прогноз-</option>
                    <option value="Старридж">Старридж</option>
                    <option value="Мане">Мане</option>
                    <option value="Вейналдум">Вейналдум</option>
                    <option value="Груич">Груич</option>
                    <option value="Фирмино">Фирмино</option>
                    <option value="Коутиньо">Коутиньо</option>
                    <option value="Ингс">Ингс</option>
                    <option value="Лаллана">Лаллана</option>
                    <option value="Маркович">Маркович</option>
                    <option value="Хендерсон">Хендерсон</option>
                    <option value="Ориги">Ориги</option>
                    <option value="Другой">Другой</option>
                </select>
            </li>
        </ul>
            <ul class="cf">
            <li style="width: 68%">До какой стадии дойдет Ливерпуль в Кубке Лиги?</li>
            <li style="width: 7%">
                <select name="5" onchange="getSeasonSelectData(this, this.name)">
                    <option disabled selected value>-сделать прогноз-</option>
                    <option value="Второй раунд">Второй раунд</option>
                    <option value="Третий раунд">Третий раунд</option>
                    <option value="Четвертый раунд">Четвертый раунд</option>
                    <option value="Пятый раунд">Пятый раунд</option>
                    <option value="Полуфинал">Полуфинал</option>
                    <option value="Финал">Финал</option>
                </select>
            </li>
        </ul>
            <ul class="cf">
            <li style="width: 68%">До какой стадии дойдет Ливерпуль в Кубке Англии?</li>
            <li style="width: 7%">
                <select name="6" onchange="getSeasonSelectData(this, this.name)">
                    <option disabled selected value>-сделать прогноз-</option>
                    <option value="Третий раунд">Третий раунд</option>
                    <option value="Четвертый раунд">Четвертый раунд</option>
                    <option value="Пятый раунд">Пятый раунд</option>
                    <option value="Четвертьфинал">Четвертьфинал</option>
                    <option value="Полуфинал">Полуфинал</option>
                    <option value="Финал">Финал</option>
                </select>
            </li>
        </ul>
            <ul class="cf">
            <li style="width: 68%">Сколько голов забьёт Старридж во всех играх сезона 2016/17?</li>
            <li style="width: 7%">
                <select name="7" onchange="getSeasonSelectData(this, this.name)">
                    <option disabled selected value>-сделать прогноз-</option>
                    <option value="0-5">0-5</option>
                    <option value="6-9">6-9</option>
                    <option value="10-12">10-12</option>
                    <option value="13-14">13-14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21-23">21-23</option>
                    <option value="24-26">24-26</option>
                    <option value="27 и больше">27 и больше</option>
                </select>
            </li>
        </ul>
            <ul class="cf">
            <li style="padding-left: 40%">
                <input type="button"  onclick="sendSeasonForecast()" value="Отправить данные" />
            </li>
        </ul>
    </form>
    <?php endif;?>
</div>
<div class="content_block" id="results" style="display: none">
    <br>
    <table id="new-table-report" width="100%" border="0" cellspacing="0" cellpadding="5"  style="border:1px solid #FF0000; font-size: 100%"  class="new-table-report">
        <thead
        <tr>
        <th style="border-left: 1px solid #FF0000; border-right: 1px solid #FF0000;border-top: 1px solid #FF0000;">#</th>
        <th style="border-left: 1px solid #FF0000; border-right: 1px solid #FF0000;border-top: 1px solid #FF0000;">Никнейм</th>
        <th colspan="3" style="border: 1px solid #FF0000; width: 35%"><?=$report[1]['game_name']?></th>
        <th style="border-left: 1px solid #FF0000; border-right: 1px solid #FF0000;border-top: 1px solid #FF0000;">Итого п. очков</th>
        <th style="border-left: 1px solid #FF0000; border-right: 1px solid #FF0000;border-top: 1px solid #FF0000;"">Итого</th>
        <th style="border-left: 1px solid #FF0000; border-right: 1px solid #FF0000;border-top: 1px solid #FF0000;">Бонус за сезон</th>
        <th style="border-left: 1px solid #FF0000; border-right: 1px solid #FF0000;border-top: 1px solid #FF0000;">Итого</th>
        </tr>
        <tr>
            <th style="border-right:1px solid #FF0000; border-left: 1px solid #FF0000; border-bottom: 1px solid #FF0000; "></th>
            <th style="border-right:1px solid #FF0000; border-left: 1px solid #FF0000; border-bottom: 1px solid #FF0000; "></th>
            <th style="border: 1px solid #FF0000">Прогноз</th>
            <th style="border: 1px solid #FF0000">П.очки</th>
            <th style="border: 1px solid #FF0000">Очки</th>
            <th style="border-right:1px solid #FF0000; border-left: 1px solid #FF0000; border-bottom: 1px solid #FF0000; "></th>
            <th style="border-right:1px solid #FF0000; border-left: 1px solid #FF0000; border-bottom: 1px solid #FF0000; "></th>
            <th style="border-right:1px solid #FF0000; border-left: 1px solid #FF0000; border-bottom: 1px solid #FF0000; "></th>
            <th style="border-right:1px solid #FF0000; border-left: 1px solid #FF0000; border-bottom: 1px solid #FF0000; "></th>
        </tr>
        <thead>
        <?php foreach ($report as $reportLine => $reportRow): ?>
        <tr>
            <td style="border: 1px solid #FF0000"><?=$reportLine;?></td>
            <td style="border: 1px solid #FF0000"><?=$reportRow['user_name']?></td>
            <td style="border: 1px solid #FF0000"><?=$reportRow['forecast']?></td>
            <td style="border: 1px solid #FF0000"><?=$reportRow['frcst_potential_points']?></td>
            <td style="border: 1px solid #FF0000"><?=$reportRow['frcst_points']?></td>
            <td style="border: 1px solid #FF0000"><?=$reportRow['potential_points']?></td>
            <td style="border: 1px solid #FF0000"><?=$reportRow['points']?></td>
            <td style="border: 1px solid #FF0000"><?=$reportRow['season_bonus']?></td>
            <td style="border: 1px solid #FF0000"><?=$reportRow['points']?></td>
        </tr>
            <?php $reportLine+1?>
        <?php endforeach;?>
    </table>
</div>
<div class="content_block" id="games_list" style="float: left">
    <?php
    foreach($games as $gameNumber => $game): ?>
        <?php if ($game['c_where'] == 'H'): ?>
            <?php $homeTeam = 'Ливерпуль'; ?>
        <?php $awayTeam = $game['t_name']; ?>
        <?php elseif ($game['c_where'] == 'A'): ?>
            <?php $homeTeam = $game['t_name']; ?>
        <?php $awayTeam = 'Ливерпуль'; ?>
        <?php endif; ?>
        <?php  date_default_timezone_set('Europe/London');$current_ts = strtotime(date("d-m-Y H:i:s", time()+3600)); $deadline =strtotime($game['c_date'] . ' ' . $game['c_time']);?>
        <?php if($current_ts>$deadline){$isDisabled = 'disabled';} else {$isDisabled = '';}?>
        <form id="<?= $gameNumber+1?>" method="post" class="forecast-form">
            <table width="100%" border="0" cellspacing="0" cellpadding="5"  style="border:1px solid #979595; font-size: 100%; padding: 1px">
                <tr>
                <input type="hidden" name="game_id" value="<?= $game['c_id'] ?>">
                <input type="hidden" name="game_place" value="<?= $game['c_where'] ?>">
                <input type="hidden" name="contest_id" value="<?= $contest->getId()?>">
                <td style="width: 5%"><?= $gameNumber+1?></td>
                <td style="width: 20%"><?= $homeTeam ?></td>
                <td style="width: 10%">
                    <select name="H" onchange="getGoalsValue(this, this.name)">
                        <?= renderForecastSelect($game, $forecasts, $game['c_where'], 'fS'); ?>
                    </select>
                </td>
                <td style="width: 10%">
                    <select name="A" onchange="getGoalsValue(this, this.name)">
                        <?= renderForecastSelect($game, $forecasts, $game['c_where'], 'sS'); ?>
                    </select>
                </td>
                <td style="width: 28%"><?= $awayTeam; ?></td>
                <td style="width: 27%">
                    <?php if (isset($forecasts[$game['c_id']])): ?>
                        <input type="button"  style="width: 125px" <?=$isDisabled?> onclick="setForecast(this.value, <?= $gameNumber+1?>)" value="Изменить прогноз" />
                    <?php else: ?>
                        <input type="button" style="width: 125px" <?=$isDisabled?> onclick="setForecast(this.value, <?= $gameNumber+1?>)" value="Сделать прогноз" />
                    <?php endif; ?>
                </td>
                <input type="hidden" name="user_id" value="<?= $user->id?>">
                </tr>
            </table>
        </form>
<?php endforeach; ?>
</div>
</body>
