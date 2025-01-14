function showmsg() {
    var msg = document.getElementById("msgin").value.trim();
    var file = document.getElementById("fileInput").files[0];
    
    if (msg !== "" || file) {
        var messageContainer = document.getElementById("messageContainer");
        var newMessageDiv = document.createElement("div");
        newMessageDiv.className = "d-flex justify-content-end mb-4";
        
        if (msg !== "") {
            newMessageDiv.innerHTML = `
                <div class="msg_cotainer_send">
                    ${msg}
                    <br>
                    <span class="msg_time_send">9:10 AM, Today</span>
                </div>
                <div class="img_cont_msg">
                    <img src="profilemine.png" class="rounded-circle user_img_msg">
                </div>
            `;
        } else {
            var fileName = file.name;
            newMessageDiv.innerHTML = `
                <div class="msg_cotainer_send">
                    <p>File: ${fileName}</p>
                    <br>
                    <span class="msg_time_send">9:10 AM, Today</span>
                </div>
                <div class="img_cont_msg">
                    <img src="profilemine.png" class="rounded-circle user_img_msg">
                </div>
            `;
        }
        
        messageContainer.appendChild(newMessageDiv);
        document.getElementById("msgin").value = ""; 
        document.getElementById("fileInput").value = ""; 
    }
}