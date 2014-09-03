$('form').validate({
	rules: {
		email: {
			minlength: 3,
			maxlength: 255,
			required: true
		},
		password: {
			minlength: 6,
			required: true
		},
		verifyPassword: {
			equalTo: '#password',
			required: true
		},
		name: {
			minlength: 3,
			maxlength: 255,
			required: true
		},
		address: {
			required: true
		},
		phone: {
			minlength: 5,
			maxlength: 255,
			required: true
		}
	},
	highlight: function(element) {
		$(element).closest('.form-group').addClass('has-error');
	},
	unhighlight: function(element) {
		$(element).closest('.form-group').removeClass('has-error');
	},
	errorElement: 'span',
	errorClass: 'help-block',
	errorPlacement: function(error, element) {
		if(element.parent('.input-group').length) {
			error.insertAfter(element.parent());
		} else {
			error.insertAfter(element);
		}
	}
});