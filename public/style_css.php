<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Helvetica', sans-serif; /* Set Helvetica as the default font */
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh; /* Ensure full viewport height */
  margin: 0; /* Remove default body margin */
  background-color: DarkSlateGrey;
}

/* Customize scrollbar */
::-webkit-scrollbar {
  width: 10px; /* Set the width of the scrollbar */
}

::-webkit-scrollbar-track {
  background: transparent; /* Set the background color of the track */
}

::-webkit-scrollbar-thumb {
  background: DarkGray; /* Set the color of the thumb */
  border-radius: 5px; /* Add rounded corners to the thumb */
}

#dashboard-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: beige; /* Set background color for the dashboard content */
  padding: 20px; /* Add padding for better spacing */
  margin: 20px 0; /* Add top and bottom margin */
  border-radius: 10px; /* Add rounded corners */
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
  overflow-y: auto;
  max-height: 95vh;
}

h2, p {
  text-align: center; /* Center text */
  margin: 5px;
}

.modify-player-input {
  width: 85px;
}

.inner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: beige; /* Set background color for the dashboard content */
  padding: 3px; /* Add padding for better spacing */
  margin-bottom: 8px;
  border-radius: 10px; /* Add rounded corners */
  box-shadow: 0 0 3px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
}

#addPlayerForm {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.toggle-buttons, #logout-btn, #generateTeamsButton, #sessionDeleteButton, #add-player-btn, button {
  background-color: DarkSeaGreen;
  color: DarkSlateGrey;
  padding: 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin: 5px;
}

#addPlayerForm {
  display: none; /* Hide the form by default */
}

.toggle-buttons {
  margin: 10px 15px; /* Adjust as needed */
}

/* Style for court divs */
.court {
  display: flex;
  flex-direction: row; /* Display children (teams and benches) in a row */
  border: 1px solid DarkSlateGrey;
  margin: 10px; /* Add margin for spacing between courts */
  width: 250px;
}

/* Style for team divs inside the court */
.team {
  flex: 1; /* Distribute space evenly among teams */
  padding: 10px; /* Add padding inside the team div */
  margin: 5px; /* Add margin for spacing between teams */
  border-radius: 5px; /* Add rounded corners */
  box-shadow: 0 0 3px rgba(0, 0, 0, 0.5); /* Add a subtle box shadow */
}

/* Style for bench divs inside the court */
.bench {
  flex: 1; /* Distribute space evenly among benches */
  border: 1px solid DarkRed;
  margin: 5px; /* Add margin for spacing between bench divs */
}

#players-container {
  max-height: 45vh;
  max-width: 500px;
  overflow-x: hidden; /* Disable horizontal scrollbar */
  overflow-y: auto;
  border: 1px solid DarkSlateGrey;
  margin-top: 10px;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  flex-direction: row; /* Set to row to stack from left to right */
}

/* Customize the appearance of the horizontal scrollbar */
#players-container::-webkit-scrollbar {
  height: 8px; /* Set the height of the scrollbar */
  width: 8px; /* Set the width of the scrollbar */
}

#players-container::-webkit-scrollbar-track {
  background-color: transparent; /* Set the background color of the scrollbar track */
}

#players-container::-webkit-scrollbar-thumb {
  background-color: DarkSlateGrey; /* Set the color of the scrollbar thumb */
  border-radius: 4px; /* Add rounded corners to the scrollbar thumb */
}

.player-container {
  font-family: 'Helvetica', sans-serif;
  padding: 1px;
  border-bottom: 1px solid #ddd;
  box-sizing: border-box;
  width: 25%; /* Adjust the width for two columns with some spacing */
  margin-bottom: 5px; /* Add margin between player-containers */
  display: flex;
  flex-direction: column;
  align-items: center;
}

#generateTeamsForm {
  display: flex;
  flex-direction: column;
  align-items: center; /* Align items to the start (left) of the container */
  margin: 10px;
}

#numCourts {
 width: 50px;
}

.generateTeamsForm-inner-container {
  padding: 0 20px;
}

/* Add your custom styles here for the logout button */
#logout-container {
  position: absolute;
  top: 10px;
  right: 10px;
}

#logout-btn {
  background-color: #f44336;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
</style>
