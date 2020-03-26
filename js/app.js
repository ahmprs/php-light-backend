function signIn() {
	$.post('./srv/api/sign-in/new-seed', {}, function(d, s) {
		console.log({ d, s });
		try {
			if (d['ok'] == 1) {
				var seed = d['result'];
				var user_name = $('#txt_user_name_sign_in').val();
				var user_pass = $('#txt_user_pass_sign_in').val();
				var h = getMd5(user_pass);
				var otp = getMd5(h + seed);

				$.post('./srv/api/sign-in', { user_name, otp }, function(dd, ss) {
					try {
						console.log({ dd, ss });
					} catch (err) {
						console.log(err);
					}
				});
			}
		} catch (err) {
			console.log(err);
		}
	});
}

function signUp() {
	var user_name = $('#txt_user_name_sign_up').val();
	var user_pass = $('#txt_user_pass_sign_up').val();
	var user_pass_hash = getMd5(user_pass);
	$.post('./srv/api/sign-up', { user_name, user_pass_hash }, (d, s) => {
		console.log({ d, s });
	});
}

function changePassword() {
	var user_pass = $('#txt_user_pass_sign_in').val();
	var user_pass_hash = getMd5(user_pass);

	$.post('./srv/api/sign-in/change-password', { user_pass_hash }, function(d, s) {
		try {
			console.log({ d, s });
		} catch (err) {
			console.log(err);
		}
	});
}
