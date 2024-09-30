<script>
/**
 * TODO:
 *  - Fix the random slogrithm team generation to create courts also directly and refactor everything accordingly
 *  - add cloudflare CAPTCHA
 *  - add a system to limit the number of login attempts
 *  - add acount for selected players and options to deselect and select everyone
 */

let playerData = [];

$(document).ready(function() {
    // Toggle the visibility of the form on button click
    $('#toggleAddPlayerForm').on('click', function() {
        $('#addPlayerForm').toggle();
        // Update button text based on the visibility state
        let buttonText = $('#addPlayerForm').is(':visible') ? "Cacher l'ajout d'un joueur" : 'Ajouter un joueur';
        $(this).text(buttonText);
    });

    $('#togglePlayerList').on('click', function () {
        $('#players-container').toggle(); 
        // Update button text based on the visibility state
        let buttonText = $('#players-container').is(':visible') ? 'Cacher les joueurs' : 'Afficher les joueurs';
        $(this).text(buttonText);

        if ($('#players-container').is(':visible')) {
            $('#teams-container').hide();
            $('#toggle-teams-button').text('Afficher les équipes');
        }
    })

    //toggle visibility of teams
    $('#toggle-teams-button').on('click', function() {
        $('#teams-container').toggle(); 
        let buttonText = $('#teams-container').is(':visible') ? 'Cacher les équipes' : 'Afficher les équipes';
        $(this).text(buttonText);
        //hide players
        if ($('#teams-container').is(':visible')) {
            $('#players-container').hide();
            $('#togglePlayerList').text('Show Player List');
        }
    });

    //hide by default
    $('#players-container').hide();

    // Perform AJAX request to fetch players
    $.ajax({
        url: 'fetch_players.php',
        method: 'POST',
        dataType: 'json',
        success: function(players) {
            // Check if the response is an array
            if (Array.isArray(players)) {
                // Iterate through the players and append div for each player
                $.each(players, function(index, player) {
                    let playerDiv = '<div class="player-container">';
                    playerDiv += '<p class="player-container-p"><strong>' + player.name + '</strong></p>';
                    playerDiv += '<p class="player-container-p">Nv: ' + player.level + '</p>';
                    let checked = ""
                    player.active ? checked = "checked" : checked = "";
                    playerDiv += '<input type="checkbox" class="inactive-checkbox" data-player-id="' + player.id + '" ' + checked + '> Présent';
                    playerDiv += '<button class="modify-player" data-player-id="' + player.id + '">Modifier</button>';            
                    playerDiv += '<button class="delete-player" data-player-id="' + player.id + '">Supprimer</button>';
                    playerDiv += '</div>';

                    // Append the player div to the container
                    $('#players-container').append(playerDiv);
                });

                // Bind checkBox inactive
                $('.inactive-checkbox').on('change', function() {
                    let playerId = $(this).data('player-id');
                    let isChecked = $(this).prop('checked') ? 1 : 0;

                    // Perform AJAX request to set player as inactive
                    $.ajax({
                        url: 'set_inactive.php',
                        method: 'POST',
                        data: { playerId: playerId, active: isChecked },
                        success: function(response) {
                            // Handle success
                            console.log('Player state updated successfully');
                            console.log(response);
                        },
                        error: function(error) {
                            // Handle error
                            console.error('Error updating player state:', error);
                        }
                    });

                    countActivePlayers();
                });

                // Bind click event to dynamically created delete buttons
                $('.delete-player').on('click', function() {
                    let playerIdToDelete = $(this).data('player-id');

                    // Store a reference to 'this'
                    let clickedButton = $(this);

                    // Perform AJAX request to delete player
                    $.ajax({
                        url: 'delete_player.php',
                        method: 'POST',
                        data: { playerId: playerIdToDelete },
                        success: function(response) {
                            // Handle success (e.g., remove the div from the container)
                            console.log('Player deleted successfully');
                            clickedButton.parent('.player-container').remove();
                        },
                        error: function(error) {
                            // Handle error
                            console.error('Error deleting player:', error);
                        }
                    });
                });

                //bind modify button
                $('.modify-player').on('click', function () {
                    let playerId = $(this).data('player-id');
                    let playerDiv = $(this).parent();
                    let playerName = playerDiv.find('p:first').text();
                    let playerLevelString = playerDiv.find('p:eq(1)').text();
                    const levelRegex = /\d+/
                    const match = playerLevelString.match(levelRegex);
                    let playerLevel
                    if (match) {
                        playerLevel = match[0];
                    }
                    else {
                        playerLevel = "NULL";
                    }

                    //empty the div
                    playerDiv.empty();

                    //replace by form with same values
                    let playerForm = `
<form class="modify-player-form" action="" method="post">
<input type="text" id="playerName" class="modify-player-input" name="playerName" value=${playerName} required>
<input type="number" id="playerLevel" class="modify-player-input" name="playerLevel" min="1" max="10" value=${playerLevel} required>

<button class="applyModifyPlayer" name="modifyPlayer" data-player-id=${playerId}>Modifier</button>
<button type="submit" name="returnModifyPlayer">Annuler</button>
</form>
`;
                    playerDiv.append(playerForm);

                    //add button events
                    $(".applyModifyPlayer").on("click", function (e) {
                        e.preventDefault();
                        let playerId = $(this).data('player-id');
                        let playerName = $(this).parent().find('input:first').val();
                        let playerLevel = $(this).parent().find('input:eq(1)').val();

                        // Perform AJAX request to delete player
                        $.ajax({
                            url: 'modify_player.php',
                            method: 'POST',
                            data: { playerId: playerId, name: playerName, level: playerLevel },
                            success: function(response) {
                                // Handle success (e.g., remove the div from the container)
                                console.log(response);
                                console.log('Player modified successfully');
                                location.reload(true);
                            },
                            error: function(error) {
                                // Handle error
                                console.error('Error modifying player:', error);
                            }
                        });

                    });

                });

                countActivePlayers();
            } else {
                console.error('Invalid response format. Expected an array.');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching players:', textStatus, errorThrown);
            console.log(jqXHR.responseText); // Log the full responseText
        }
    });

    function countActivePlayers() {
        let activeCount = 0;

        // Loop through each checkbox with the class 'inactive-checkbox'
        $('.inactive-checkbox').each(function() {
            // Check if the checkbox is checked
            if ($(this).is(':checked')) {
                activeCount++;
            }
        });

        // Update the span with the id 'active-player-count' with the active count
        $('#active-player-count').text(activeCount);
    }

    function generateTeams({ save: saveFlag }) {
        // Serialize the form data
        let formData = $("#generateTeamsForm").serialize();
        // add saveFlag
        formData+= `&postSwitch=${saveFlag}`;
        // add pastData
        if ($('#teams-container').data("data") !== undefined) {
            formData+= `&pastData=${JSON.stringify($('#teams-container').data("data"))}`
        }


        //hide players
        if ($('#players-container').is(':visible')) {
            $('#players-container').hide();
            $('#togglePlayerList').text('Afficher les joueurs');
        }

        //show teams
        $('#teams-container').show();
        $('#toggle-teams-button').text('Cacher les équipes');

        // Send an AJAX request
        $.ajax({
            type: "POST",
            url: "generate_teams.php",
            data: formData,
            success: function (response) {
                // Display the response in the teams-container div
                $("#session-active-flag").text("OUI!"); 
                $('#session-active-flag').css('color', 'green');
                console.log(response);

                // Clear the existing content in teams-container
                $("#teams-container").empty();
                $("#teams-container").data("data", response.courts);

                // Create divs for each court and teams
                for (let i = 1; i <= response['courtNumber']; i++) {
                    // Create a div for the court
                    let courtDiv = $("<div>").addClass("court").appendTo("#teams-container");

                    if (i <= response.courts.courts.length) {
                        // Create divs for each team in the court
                        for (let j = 0; j < 2; j++) {
                            let teamDiv = $("<div>").addClass("team").appendTo(courtDiv);

                            let team = response.courts.courts[i - 1][j];

                            // Append player1 and player2 to the team div
                            $("<p>").text(team.player1.name).appendTo(teamDiv);
                            $("<p>").text(team.player2.name).appendTo(teamDiv);
                        }
                    }

                }

                // Add bench players
                let benchDiv = $("<div>").addClass("bench").appendTo("#teams-container");
                for (let i = 0; i < response.courts.bench.length; i++) {
                    $("<p>").text(response.courts.bench[i].player1.name).appendTo(benchDiv);
                }

                // append the error/redo option
                let redoTeamsButton = $("<p>(En cas d'erreur, cliquer sur ce texte afin de re-générer les équipes sans ajouter les précédentes à l'historique)</p>").attr("id", "redoTeams").appendTo("#teams-container");
                $('#redoTeams').click(function() {
                    generateTeams({ save: false });
                    console.log("redoing teams... need to code.");
                });

            },
            error: function (error) {
                console.log("Error:", error);
            }
        });
    }

    $("#generateTeamsButton").click(function() {
        generateTeams({ save: true })
    });
    //

    $("#sessionDeleteButton").click(function () {
        $.ajax({
            type: "POST",
            url: "resetTeamSession.php",
            success: function (response) {
                $("#session-active-flag").text("Aucune"); 
                $('#session-active-flag').css('color', 'red');
                $("#teams-container").removeData();
            },
            error: function (error) {
                console.log("Error:", error);
            }
        });
    });

    //check session state on page load
    $.ajax({
        type: "POST",
        url: "checkSessionState.php",
        success: function (response) {
            console.log(response);
            // Update button text based on the visibility state
            let flagText = response['isEmpty'] ? 'Aucune' : 'OUI';
            $('#session-active-flag').text(flagText);
            flagText === 'Aucune' ? $('#session-active-flag').css('color', 'red') : $('#session-active-flag').css('color', 'green');
        },
        error: function (error) {
            console.log("Error:", error);
        }
    });

    function checkBalanceTeamSwitch() {
        let algorithm = $('input[name="algorithm"]:checked').val();

        // Check if the radio button is selected
        if(algorithm === "matchLevel") {
            // If selected, check the checkbox and disable it
            $('#balance-courts-switch').prop('checked', true);
            $('#balance-courts-switch').prop('disabled', true);
        } else if(algorithm === "random") {
            // If selected, check the checkbox and disable it
            $('#balance-courts-switch').prop('checked', false);
            $('#balance-courts-switch').prop('disabled', false);
        } 
    }

    // Update the algorithm selection when the user changes the radio button
    $('input[name="algorithm"]').on('change', function () {
        let newAlgorithm = $('input[name="algorithm"]:checked').val();

        checkBalanceTeamSwitch();

        // fetch balance team switch selection HERE

        // Update the algorithm selection in the database
        $.ajax({
            type: 'POST',
            url: 'update_algorithm_selection.php',
            data: { algorithmSelection: newAlgorithm },
            success: function (response) {
                console.log('Algorithm selection updated successfully');
            },
            error: function (error) {
                console.log('Error updating algorithm selection:', error);
            }
        });
    });

    // update the numCourts in the db when changed
    $('#numCourts').on('change', function() {
        let newNumCourts = $(this).val();

        // update the numCourts in db
        $.ajax({
            type: 'POST',
            url: 'update_numCourts.php',
            data: { numCourts: newNumCourts },
            success: function (response) {
                console.log('numCourts selection updated successfully');
            },
            error: function (error) {
                console.log('Error updating numCourts selection:', error);
            }
        });
    });

    // Fetch current algorithm selection on page load
    $.ajax({
        type: 'GET',
        url: 'get_algorithm_selection.php',
        success: function (response) {
            // Update the radio button based on the fetched algorithm selection
            if (response.algorithmSelection === 'random') {
                $('#randomAlgorithm').prop('checked', true);
            } else if (response.algorithmSelection === 'matchLevel') {
                $('#matchLevelAlgorithm').prop('checked', true);
            }
            checkBalanceTeamSwitch();
        },
        error: function (error) {
            console.log('Error fetching algorithm selection:', error);
        }
    });

    // Fetch current numCourts selection on page load
    $.ajax({
        type: 'GET',
        url: 'get_numCourts.php',
        success: function (response) {
            // update the numCourts
            $('#numCourts').val(response.numCourts);
        },
        error: function (error) {
            console.log('Error fetching numCourts selection:', error);
        }
    });

});
</script>
