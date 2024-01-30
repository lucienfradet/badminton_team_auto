<script>
/**
 * TODO:
 *
 * OK REWRITE ALL DB so that users are in "users" table to prevent sql injection
 * follow this format:

    *CREATE TABLE users (
      username TEXT PRIMARY KEY,
      password TEXT,
      timestamp TEXT,
      team_array TEXT,
      algorithm_selection TEXT
    );

    Insertion example:
    INSERT INTO users (username, password, timestamp, team_array, algorithm_selection)
    VALUES ('john_doe', '$2y$10$RiDJkFdm4WJUbxB7tKX1xOBz7UdE1TAKVYKFvBejTV8wGJ.gjGJ4e', '2022-01-19 12:34:56', '[team1, team2]', 'algorithm1');
 *
 *  OK make it so changing algorithm resets the session
 *  OK Make the login page safe and make sure all the pages are secure and can't be accesed without using ajax
 *  OK Deploy using ngnix and docker
 *
 *  OK Restarting the db and creating users for the different groups with passwords
 *  OK beautifying the page
 *  
 *  - add cloudflare CAPTCHA
 *  - add a system to limit the number of login attempts
 */

let playerData = [];

$(document).ready(function() {
  // Toggle the visibility of the form on button click
  $('#toggleAddPlayerForm').on('click', function() {
    $('#addPlayerForm').toggle();
    // Update button text based on the visibility state
    let buttonText = $('#addPlayerForm').is(':visible') ? 'Hide new player form' : 'Add New Player';
    $(this).text(buttonText);
  });

  $('#togglePlayerList').on('click', function () {
    $('#players-container').toggle(); 
    // Update button text based on the visibility state
    let buttonText = $('#players-container').is(':visible') ? 'Hide Player List' : 'Show Player List';
    $(this).text(buttonText);

    if ($('#players-container').is(':visible')) {
      $('#teams-container').hide();
      $('#toggle-teams-button').text('Show Teams');
    }
  })

  //toggle visibility of teams
  $('#toggle-teams-button').on('click', function() {
    $('#teams-container').toggle(); 
    let buttonText = $('#teams-container').is(':visible') ? 'Hide Teams' : 'Show Teams';
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
        console.log(players);
        // Check if the response is an array
        if (Array.isArray(players)) {
          // Iterate through the players and append div for each player
          $.each(players, function(index, player) {
            let playerDiv = '<div class="player-container">';
            playerDiv += '<p><strong>' + player.name + '</strong></p>';
            playerDiv += '<p>Level: ' + player.level + '</p>';
            let checked = ""
            player.active ? checked = "checked" : checked = "";
            playerDiv += '<input type="checkbox" class="inactive-checkbox" data-player-id="' + player.id + '" ' + checked + '> Active';
            playerDiv += '<button class="modify-player" data-player-id="' + player.id + '">Modify</button>';            
            playerDiv += '<button class="delete-player" data-player-id="' + player.id + '">Delete</button>';
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
<input type="number" id="playerLevel" class="modify-player-input" name="playerLevel" value=${playerLevel} required>

<button class="applyModifyPlayer" name="modifyPlayer" data-player-id=${playerId}>Modify</button>
<button type="submit" name="returnModifyPlayer">Abord</button>
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

      } else {
          console.error('Invalid response format. Expected an array.');
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.error('Error fetching players:', textStatus, errorThrown);
      console.log(jqXHR.responseText); // Log the full responseText
    }
  });

  $("#generateTeamsButton").click(function () {
    // Serialize the form data
    let formData = $("#generateTeamsForm").serialize();
    //hide players
    if ($('#players-container').is(':visible')) {
      $('#players-container').hide();
      $('#togglePlayerList').text('Show Players');
    }

    //show teams
    $('#teams-container').show();
    $('#toggle-teams-button').text('Hide Teams');

    // Send an AJAX request
    $.ajax({
      type: "POST",
      url: "generate_teams.php",
      data: formData,
      success: function (response) {
        // Display the response in the teams-container div
        $("#session-active-flag").text("YES!"); 
        $('#session-active-flag').css('color', 'green');
        console.log(response);

        // Clear the existing content in teams-container
        $("#teams-container").empty();

        // Create divs for each court and teams
        let counter = 0;
        for (let i = 1; i <= response['courtNumber']; i++) {
          // Create a div for the court
          let courtDiv = $("<div>").addClass("court").appendTo("#teams-container");
          // Create divs for each team in the court
          for (let j = 1; j <= 2; j++) {
            let teamDiv = $("<div>").addClass("team").appendTo(courtDiv);

            let team = undefined;
            if (counter < response.teams.length) {
              team = response.teams[counter];
              //break out if the last team is not a team
              if (team['player2'] == null) {
                break;
              }
            } 
            else {
              break;
            }

            // Append player1 and player2 to the team div
            $("<p>").text(team['player1']).appendTo(teamDiv);
            $("<p>").text(team['player2']).appendTo(teamDiv);
            counter++;
          }

          // Check if the second team in the current court has empty content
          let teamsInCurrentCourt = courtDiv.children(".team");
          if (teamsInCurrentCourt.length === 2) {
            if (teamsInCurrentCourt.eq(1).html() === "") {
              // There is only one team in the last court, make an additional AJAX request
              let team = courtDiv.find(".team").eq(0);

              $.ajax({
                type: "POST",
                url: "addBenchPlayers.php",
                data: {
                  player1: team.find("p:eq(0)").text(),
                  player2: team.find("p:eq(1)").text()
                },
                success: function (result) {
                  console.log("Data added to team_array successfully:", result);
                },
                error: function (error) {
                  console.log("Error adding data to team_array:", error);
                }
              });
            }
          }
        }

        if (counter < response.teams.length) {
          let benchDiv = $("<div>").addClass("bench").appendTo("#teams-container");
          for (let i = counter; i < response.teams.length; i++) {
            $("<p>").text(response.teams[i]['player1']).appendTo(benchDiv);
            $("<p>").text(response.teams[i]['player2']).appendTo(benchDiv);
            //add bench players to db if not already in empty team
            if (response.teams[i]['player2'] !== null) {
              $.ajax({
                type: "POST",
                url: "addBenchPlayers.php",
                data: {
                  player1: response.teams[i]['player1'],
                  player2: response.teams[i]['player2']
                },
                success: function (result) {
                  console.log("Data added to team_array successfully:", result);
                },
                error: function (error) {
                  console.log("Error adding data to team_array:", error);
                }
              });
            }
          }
        } 

      },
      error: function (error) {
        console.log("Error:", error);
      }
    });
  });

  $("#sessionDeleteButton").click(function () {
    $.ajax({
      type: "POST",
      url: "resetTeamSession.php",
      success: function (response) {
        $("#session-active-flag").text("None"); 
        $('#session-active-flag').css('color', 'red');
      },
      error: function (error) {
        console.log("Error:", error);
      }
    });
  });

  //Reset session if algorithm is changed
  // $('input[name="algorithm"]').change(function() {
  //   // Make an AJAX post request
  //   $.ajax({
  //     type: 'POST',
  //     url: "resetTeamSession.php",
  //     success: function(response) {
  //       console.log(response);
  //       $('#session-active-flag').text("None");
  //     },
  //     error: function(error) {
  //       console.log("Error:", error);
  //     }
  //   });
  // });

  //check session state on page load
  $.ajax({
    type: "POST",
    url: "checkSessionState.php",
    success: function (response) {
      console.log(response);
      // Update button text based on the visibility state
      let flagText = response['isEmpty'] ? 'None' : 'YES';
      $('#session-active-flag').text(flagText);
      flagText === 'None' ? $('#session-active-flag').css('color', 'red') : $('#session-active-flag').css('color', 'green');
    },
    error: function (error) {
      console.log("Error:", error);
    }
  });

  // Update the algorithm selection when the user changes the radio button
  $('input[name="algorithm"]').on('change', function () {
    let newAlgorithm = $('input[name="algorithm"]:checked').val();

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
