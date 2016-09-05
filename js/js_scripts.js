/**
 * Created by talipa on 8/7/2016.
 */
var goalsH = 0;
var goalsA = 0;
var lfcPlace;
var lfcAllGoals;
var lfcGamesAtNull;
var lfcBestGoalScorer;
var ligueCupStage;
var faCupStage;
var sturridgeGoals;
function getGoalsValue(element, place) {
    if (place == "H") {
        goalsH = element.options[element.selectedIndex].value;
    }
    else {
        goalsA = element.options[element.selectedIndex].value;
    }
}

function openForecast(evt, divId) {

    var i, content_block, top_tab;
    content_block = document.getElementsByClassName("content_block");
    for (i = 0; i < content_block.length; i++) {
        content_block[i].style.display = "none";
    }
    top_tab = document.getElementsByClassName("top_tab");
    for (i = 0; i < top_tab.length; i++) {
        top_tab[i].className = top_tab[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the link that opened the tab
    document.getElementById(divId).style.display = "block";
    evt.currentTarget.className += " active";
	
   return false;
}

function setForecast(method, formId){

    document.getElementById("forecastBody").disabled=true;
    var div = document.getElementById("games_list");
    jQuery(document).ready(function($){
        $('.forecast-form').each(function(){
            var T = $(this);
            T.click(function(e){
                e.preventDefault();
                var forecastData = T.serialize();
                var game_id = forecastData.substr(8,4);
                var game_place = forecastData.substr(24,1);
                var contest_id = forecastData.substr(37,1);
                var pos = forecastData.indexOf("user_id");
                var user_id = forecastData.substr(pos);
                user_id = user_id.substr(8);
                var fSelect = div.getElementsByTagName("select")[2*formId-2].onchange();
                var sSelect = div.getElementsByTagName("select")[2*formId-1].onchange();
                if (method == "Сделать прогноз"){
                    method = "insert";
                }
                else method = "update";
                var url = 'index2.php?option=com_user&task=loadDataToDB';
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {game_id: game_id,game_place: game_place, contest_id: contest_id, user_id: user_id, method: method, goals_h: goalsH, goals_a: goalsA},
                    success: function(response) {
                        alert(response);
                        location.reload();
                    }
                });
            });
        });
    });
}

function sendSeasonForecast() {
    jQuery(document).ready(function($){
        $('.forecast-season-form').each(function(){
            var T = $(this);
            T.click(function(e){
                e.preventDefault();
                var forecastSeasonData = T.serialize();
                var user_id = forecastSeasonData.substr(21);
                var contest_id = forecastSeasonData.substr(11,1);
                $.ajax({
                    type: 'POST',
                    url: 'index2.php?option=com_user&task=loadSeasonForecastToDB',
                    data: {contest_id: contest_id, user_id: user_id, lfc_place: lfcPlace, lfc_all_goals: lfcAllGoals, lfc_games_at_null: lfcGamesAtNull,lfc_best_goal_scorer: lfcBestGoalScorer,ligue_cup_stage: ligueCupStage,fa_cup_stage: faCupStage,sturridge_goals: sturridgeGoals},
                    success: function(response) {
                        alert(response);
                        location.reload();
                    }
                });
            });
        });
    });
}
function getSeasonSelectData(element,selectId) {
    switch (selectId){
        case "1":
            lfcPlace= element.options[element.selectedIndex].value;
            break;
        case "2":
            lfcAllGoals= element.options[element.selectedIndex].value;
            break;
        case "3":
            lfcGamesAtNull= element.options[element.selectedIndex].value;
            break;
        case "4":
            lfcBestGoalScorer= element.options[element.selectedIndex].value;
            break;
        case "5":
            ligueCupStage= element.options[element.selectedIndex].value;
            break;
        case "6":
            faCupStage= element.options[element.selectedIndex].value;
            break;
        case "7":
            sturridgeGoals= element.options[element.selectedIndex].value;
    }
}
