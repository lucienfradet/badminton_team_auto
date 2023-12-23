let playerData = [];

$(document).ready(function() {
  // Toggle the visibility of the form on button click
  $('#toggleAddPlayerForm').on('click', function() {
    $('#addPlayerForm').toggle();
  });

  $('#togglePlayerList').on('click', function () {
    $('#players-container').toggle(); 
  })

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
                playerDiv += '<p>Name: ' + player.name + '</p>';
                playerDiv += '<p>Level: ' + player.level + '</p>';
                let checked = ""
                player.active ? checked = "checked" : checked = "";
                playerDiv += '<input type="checkbox" class="inactive-checkbox" data-player-id="' + player.id + '" ' + checked + '> Active';
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
        } else {
            console.error('Invalid response format. Expected an array.');
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error('Error fetching players:', textStatus, errorThrown);
        console.log(jqXHR.responseText); // Log the full responseText
      }
  });
});
