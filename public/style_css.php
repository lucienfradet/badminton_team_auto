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
  margin: 10px; /* Remove default body margin */
  background-color: DarkSlateGrey;
}

/* Customize scrollbar */
::-webkit-scrollbar {
  width: 8px; /* Set the width of the scrollbar */
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
  padding: 15px; /* Add padding for better spacing */
  margin: 15px 0; /* Add top and bottom margin */
  border-radius: 10px; /* Add rounded corners */
  box-shadow: 0 0 2vw rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
  overflow-y: auto;
  max-height: 95vh;
  max-width: 600px;
}

h2, p {
  text-align: center; /* Center text */
  margin: 15px;
}

.modify-player-input {
  width: 20vw;
  max-width: 150px;
}

.inner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: beige; /* Set background color for the dashboard content */
  padding: 10px; /* Add padding for better spacing */
  margin-bottom: 15px;
  border-radius: 10px; /* Add rounded corners */
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
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

#generateTeamsButton {
  padding: 10px 20px; /* Add padding to the button */
  border: 2px solid transparent; /* Set border to 2px solid */
  border-radius: 5px; /* Optional: Add border-radius for rounded corners */
  box-sizing: border-box; /* Include padding and border in the button's total width and height */
  border-color: DarkSlateGrey;
}

#sessionDeleteButton {
  background-color: DarkRed;
  color: white;
  width: 30%;
}

.delete-player {
  background-color: DarkRed;
  color: white;
  padding: 5px;
}

#addPlayerForm {
  display: none; /* Hide the form by default */
}

.toggle-buttons {
  margin: 10px 15px; /* Adjust as needed */
}

#teams-container {
  width: 95%;  
}

/* Style for court divs */
.court {
  display: flex;
  flex-direction: row; /* Display children (teams and benches) in a row */
  border: 3px solid DarkSlateGrey;
  margin: 5px; /* Add margin for spacing between courts */
  width: 95%;
  min-height: 20px;
}

/* Style for team divs inside the court */
.team {
  flex: 1; /* Distribute space evenly among teams */
  padding: 5px; /* Add padding inside the team div */
  margin: 10px; /* Add margin for spacing between teams */
  border-radius: 5px; /* Add rounded corners */
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.5); /* Add a subtle box shadow */
}

/* Style for bench divs inside the court */
.bench {
  flex: 1; /* Distribute space evenly among benches */
  border: 3px solid DarkRed;
  margin: 10px; /* Add margin for spacing between bench divs */
  width: 50%;
  min-height: 20px;
}

#players-container {
  max-height: 70vh;
  max-width: 85vw;
  min-height: 60vh;
  overflow-x: hidden; /* Disable horizontal scrollbar */
  overflow-y: auto;
  border: 2px solid DarkSlateGrey;
  margin-top: 5px;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  flex-direction: row; /* Set to row to stack from left to right */
}

/* Customize the appearance of the horizontal scrollbar */
#players-container::-webkit-scrollbar {
  height: 50px; /* Set the height of the scrollbar */
  width: 10px; /* Set the width of the scrollbar */
}

#players-container::-webkit-scrollbar-track {
  background-color: transparent; /* Set the background color of the scrollbar track */
}

#players-container::-webkit-scrollbar-thumb {
  background-color: DarkSlateGrey; /* Set the color of the scrollbar thumb */
  border-radius: 5px; /* Add rounded corners to the scrollbar thumb */
}

.player-container {
  font-family: 'Helvetica', sans-serif;
  padding: 5px;
  border-bottom: 1px solid #ddd;
  box-sizing: border-box;
  width: 25%; /* Adjust the width for two columns with some spacing */
  margin-bottom: 3px; /* Add margin between player-containers */
  display: flex;
  flex-direction: column;
  align-items: center;
}

.player-container-p {
  margin: 3px 3px
}

#generateTeamsForm {
  display: flex;
  flex-direction: column;
  align-items: center; /* Align items to the start (left) of the container */
  margin: 10px;
}

#numCourts {
  width: 10vw;
  max-width: 85px;
}

.generateTeamsForm-inner-container {
  padding: 20px;
  display: flex;
  justify-content: center;
}

/* Add your custom styles here for the logout button */
#logout-container {
  position: absolute;
  top: 5px;
  right: 5px;
}

#logout-btn {
  background-color: #f44336;
  color: white;
  padding: 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

#redoTeams {
  font-size: 10pt;
  color: DarkRed;   
  cursor: pointer;
}

.select-deselect-all {
    display: flex;               /* Use flexbox */
    justify-content: center;      /* Center the buttons horizontally */
    align-items: center;          /* Center the buttons vertically */
    width: 100%;                  /* Make the div take up the full width */
    padding: 10px 0;              /* Optional padding for spacing */
}

.select-deselect-all button {
    margin: 0 10px;               /* Optional: Add spacing between the buttons */
}
</style>
