function showEditModal(apiKeyId) {
    // Get the current API key value
    var apiKey = document.querySelector("td[data-id='" + apiKeyId + "']").innerText;

    // Set the current API key value in the form
    document.getElementById("apiKeyId").value = apiKeyId;
    document.getElementById("apiKey").value = apiKey;
    document.getElementById("masterKey").value = masterKey;

    // Show the modal
    document.getElementById("editModal").style.display = "block";
}

function hideEditModal() {
    // Hide the modal
    document.getElementById("editModal").style.display = "none";
}

function updateAPIKey() {
    // Get the API key ID and value from the form
    var apiKeyId = document.getElementById("apiKeyId").value;
    var apiKey = document.getElementById("apiKey").value;
    var masterKeyStr = document.getElementById("apiKey").value;
    if (masterKeyStr === "Yes") {
        masterKey = 1;
    } else {
        masterKey = 0;
    }
    console.log(apiKey + apiKeyId);
    // Use AJAX to send a PUT request to update the API key in the database
    var xhr = new XMLHttpRequest();
    xhr.open("PUT", "update_api_key.php?id=" + apiKeyId + "&token=" + apiKey + "&master_key=" + masterKey);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Update the API key in the grid
            document.querySelector("td[data-id='" + apiKeyId + "']").innerText = apiKey;

            // Hide the modal
            hideEditModal();

            // Show a message to indicate that the update was successful
            alert("API key updated successfully!");
        } else {
            // Show an error message
            alert("Error updating API key: " + xhr.responseText);
        }
    };
    xhr.send();
}

function generateAPIKey() {
    var newToken = Math.random().toString(36).substring(2, 18) + Math.random().toString(36).substring(2, 18);
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "create_api_key.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert("A new token has been generated and added to the database successfully");
            location.reload();
        } else {
            alert("An error has occured, Please try again");
        }
    }
    xhr.send("token=" + newToken.toUpperCase());
}

function deleteAPIKey(id) {
    var xhr = new XMLHttpRequest();
    xhr.open("DELETE", "delete_api_key.php?id=" + id, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert("API key has been deleted successfully");
            location.reload();
        } else {
            alert("An error has occured, Please try again: " + xhr.responseText);
        }
    }
    xhr.send();
}


function confirmDelete(id){
    if(confirm('Are you sure you want to delete this API key?')){
      deleteAPIKey(id);
    }
}
