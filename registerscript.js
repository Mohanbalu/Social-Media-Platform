let generatedOTP;

function generateAndAlertOTP() {
    generatedOTP = Math.floor((Math.random() * 10000) + 1);
    alert("Your OTP is: " + generatedOTP);
}

function checkForm(event) {
    event.preventDefault();

    let username = document.getElementById("username");
    let isValidUsername = /^[0-9a-zA-Z._]+$/;

    let valuser = isValidUsername.test(username.value);

    let phnSelect = document.getElementById("phnSelect");
    let phn = document.getElementById("phn");
    let isvalidphn = /^[0-9]{10}$/;

    let validphn = isvalidphn.test(phn.value);

    let password = document.getElementById("password");
    let confirmPassword = document.getElementById("confirmPass");
    let enteredOTP = document.getElementById("otp").value;
    
    if (valuser) {
        username.style.backgroundColor = "lightgreen";
    } else {
        username.style.backgroundColor = "red";
    }

    if (validphn) {
        phn.style.backgroundColor = "lightgreen";
    } else {
        phn.style.backgroundColor = "red";
    }

    if (valuser && parseInt(enteredOTP) === generatedOTP && password.value === confirmPassword.value && validphn && username.value.length >= 3 && username.value.length <= 10 && password.value.length >= 8 && password.value.length <= 15) {
          
    } else {
        alert("Invalid");
    }
}
