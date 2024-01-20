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
  margin: 2vw; /* Remove default body margin */
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
  padding: 5vw; /* Add padding for better spacing */
  margin: 5vw 0; /* Add top and bottom margin */
  border-radius: 5vw; /* Add rounded corners */
  box-shadow: 0 0 2vw rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
  overflow-y: auto;
  max-height: 95vh;
}

h2, p {
  text-align: center; /* Center text */
  margin: 1vh;
}

.modify-player-input {
  width: 20vw;
}

.inner-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: beige; /* Set background color for the dashboard content */
  padding: 1vw; /* Add padding for better spacing */
  margin-bottom: 2vw;
  border-radius: 4vw; /* Add rounded corners */
  box-shadow: 0 0 1vw rgba(0, 0, 0, 0.1); /* Add a subtle box shadow */
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
  border: 0.8vw solid DarkSlateGrey;
  margin: 2vw; /* Add margin for spacing between courts */
  width: 70vw;
}

/* Style for team divs inside the court */
.team {
  flex: 1; /* Distribute space evenly among teams */
  padding: 3vw; /* Add padding inside the team div */
  margin: 2vw; /* Add margin for spacing between teams */
  border-radius: 2vw; /* Add rounded corners */
  box-shadow: 0 0 1vw rgba(0, 0, 0, 0.5); /* Add a subtle box shadow */
}

/* Style for bench divs inside the court */
.bench {
  flex: 1; /* Distribute space evenly among benches */
  border: 0.8vw solid DarkRed;
  margin: 5vw; /* Add margin for spacing between bench divs */
  width: 40vw;
}

#players-container {
  max-height: 45vh;
  max-width: 85vw;
  overflow-x: hidden; /* Disable horizontal scrollbar */
  overflow-y: auto;
  border: 0.2vw solid DarkSlateGrey;
  margin-top: 1vw;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  flex-direction: row; /* Set to row to stack from left to right */
}

/* Customize the appearance of the horizontal scrollbar */
#players-container::-webkit-scrollbar {
  height: 2vh; /* Set the height of the scrollbar */
  width: 2.5vw; /* Set the width of the scrollbar */
}

#players-container::-webkit-scrollbar-track {
  background-color: transparent; /* Set the background color of the scrollbar track */
}

#players-container::-webkit-scrollbar-thumb {
  background-color: DarkSlateGrey; /* Set the color of the scrollbar thumb */
  border-radius: 1.5vw; /* Add rounded corners to the scrollbar thumb */
}

.player-container {
  font-family: 'Helvetica', sans-serif;
  padding: 0.5vw;
  border-bottom: 1px solid #ddd;
  box-sizing: border-box;
  width: 25%; /* Adjust the width for two columns with some spacing */
  margin-bottom: 2vw; /* Add margin between player-containers */
  display: flex;
  flex-direction: column;
  align-items: center;
}

#generateTeamsForm {
  display: flex;
  flex-direction: column;
  align-items: center; /* Align items to the start (left) of the container */
  margin: 2vw;
}

#numCourts {
 width: 10vw;
}

.generateTeamsForm-inner-container {
  padding: 2vw;
}

/* Add your custom styles here for the logout button */
#logout-container {
  position: absolute;
  top: 2vh;
  right: 0vh;
}

#logout-btn {
  background-color: #f44336;
  color: white;
  padding: 3vw;
  border: none;
  border-radius: 2vw;
  cursor: pointer;
}
</style>
