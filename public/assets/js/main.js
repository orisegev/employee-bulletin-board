document.getElementById('toggle-form').addEventListener('click', function() {
    const formContainer = document.getElementById('new-message-form-container');
    const plusMinusSymbol = document.getElementById('plus-minus-symbol');
    
    if (formContainer.style.display === 'none' || formContainer.style.display === '') {
        formContainer.style.display = 'block';
        plusMinusSymbol.textContent = '-';
    } else {
        formContainer.style.display = 'none';
        plusMinusSymbol.textContent = '+';
    }
});

document.getElementById('new-message-form').addEventListener('submit', function(event) {
	event.preventDefault();
	event.stopPropagation();

	const name = document.getElementById('name').value;
	const email = document.getElementById('email').value;
	const message = document.getElementById('message').value;
	const plusMinusSymbol = document.getElementById('plus-minus-symbol');
	fetch('/api/messages.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json'
		},
		body: JSON.stringify({
			name: name,
			email: email,
			message: message
		})
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			const messagesList = document.getElementById('messages-list');
			const li = document.createElement('li');
			li.id = `message-${data.insert_id}`;
			const currentDate = new Date();
			const formattedDate = currentDate.toLocaleDateString('en-GB'); 
			const formattedTime = currentDate.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
			const finalDate = `${formattedDate} - ${formattedTime}`;
			li.innerHTML = `
				<strong>תאריך הפרסום: </strong>עכשיו<br>
				<strong>מפרסם:</strong> ${name}<br>
				<strong>דוא"ל המפרסם:</strong> ${email}<br>
				${message}
			`;
			const deleteIcon = document.createElement('div');
			deleteIcon.innerHTML = '<img title="מחק הודעה" alt="delete" src="./assets/images/delete-icon.png" width="20px" height="20px"></img>';
			deleteIcon.style.cursor = 'pointer';
			deleteIcon.style.float = 'left';
			deleteIcon.addEventListener('click', function() {
				deleteMessage(data.insert_id);
			});
			li.appendChild(deleteIcon);
			messagesList.insertBefore(li, messagesList.firstChild);

			document.getElementById('new-message-form').reset();
			document.getElementById('new-message-form-container').style.display = 'none';
			plusMinusSymbol.textContent = '+';
		} else {
			alert('שגיאה בהגשת ההודעה');
		}
	})
	.catch(error => {
		console.error('Error:', error);
		alert("הייתה שגיאה בשליחת ההודעה");
	});
});


function loadMessages() {
	fetch('/api/messages.php')
		.then(response => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json(); 
		})
		.then(data => {
			const messagesList = document.getElementById('messages-list');
			messagesList.innerHTML = ''; 
			const userToken = getCookie('user_token');
			data.forEach(message => {
				const li = document.createElement('li');
				li.id = `message-${message.id}`;
				li.innerHTML = `
					<strong>תאריך הפרסום: </strong>${message.formatted_date}<br>
					<strong>מפרסם:</strong> ${message.name}<br>
					<strong>דוא"ל המפרסם:</strong> ${message.email}<br>
					${message.message}
				`;
				if (message.user_token != null && message.user_token === userToken) {
					const deleteIcon = document.createElement('div');
					deleteIcon.innerHTML = '<img title="מחק הודעה" alt="delete" src="./assets/images/delete-icon.png" width="20px" height="20px"></img>';
					deleteIcon.style.cursor = 'pointer';
					deleteIcon.style.float = 'left';
					deleteIcon.addEventListener('click', function() {
						deleteMessage(message.id);
					});
					li.appendChild(deleteIcon);
				}
				messagesList.appendChild(li);
			});
		})
		.catch(error => {
			console.error('Error:', error);
			alert("הייתה שגיאה בטעינת ההודעות");
		});
}

function getCookie(name) {
	const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
	return match ? match[2] : null;
}

function deleteMessage(messageId) {
    const confirmation = window.confirm("האם את/ה בטוח שברצונך למחוק את ההודעה?");
    if (confirmation) {
        fetch('/api/messages.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message_id: messageId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const messageElement = document.getElementById(`message-${messageId}`);
                if (messageElement) {
                    messageElement.remove();
                }
                alert('ההודעה נמחקה בהצלחה');
            } else {
                console.error('Error:', data.error);
                alert(`שגיאה: ${data.error}`);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('הייתה שגיאה במערכת');
        });
    } else {
        console.log("המשתמש ביטל את המחיקה");
    }
}

window.onload = loadMessages;