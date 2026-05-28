document.getElementById('consultationForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const subject = document.getElementById('subject').value.trim();
    const message = document.getElementById('message').value.trim();
    const errorDiv = document.getElementById('error-messages');

    errorDiv.innerHTML = '';
    let errors = [];

    if (!name || !email || !subject || !message) {
        errors.push('sva polja moraju btiti popunjena');
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
        errors.push('Email je u losem formatu');
    }

    if (message && message.length < 10) {
        errors.push('poruka mora biti dugacka najmanje 10 karaktera');
    }

    if (errors.length > 0) {
        errorDiv.innerHTML = errors.join('<br>');
        return;
    }
    const formData = new FormData();
    formData.append('name',name);
    formData.append('email',email);
    formData.append('subject',subject);
    formData.append('message',message);

    fetch('send.php',{
        method:'POST',
        body:formData
    })
        .then(response=>response.json())
        .then(data=>{
            if (data.status === 'success'){
                errorDiv.style.color = 'green';
                errorDiv.innerHTML = 'Poruka je uspesno poslata';
                document.getElementById('consultationForm').reset();
            }
            else{
                errorDiv.style.color = 'red';
                errorDiv.innerHTML = data.message || 'greska na serveru';
            }
        })
        .catch(error=>{
            errorDiv.style.color = 'red';
            errorDiv.innerHTML = 'konekcija sa serverom nije uspesna';
            console.log('Error: ',error);
        })
});