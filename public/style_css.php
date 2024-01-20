<style>
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
  border: 1px solid black;
  margin: 10px; /* Add margin for spacing between courts */
}

/* Style for team divs inside the court */
.team {
  flex: 1; /* Distribute space evenly among teams */
  padding: 10px; /* Add padding inside the team div */
  margin: 5px; /* Add margin for spacing between teams */
  border: 1px solid black;
}

/* Style for bench divs inside the court */
.bench {
  flex: 1; /* Distribute space evenly among benches */
  border: 1px solid black;
  margin: 5px; /* Add margin for spacing between bench divs */
}

#players-container {
  max-height: 500px; /* Set the maximum height for the container */
  overflow-y: auto; /* Enable vertical scroll bar if the content overflows */
  border: 1px solid #ccc; /* Optional: Add a border for better visualization */
}

.player-container {
  /* Your styling for individual player divs */
  padding: 10px;
  border-bottom: 1px solid #ddd;
}
</style>
