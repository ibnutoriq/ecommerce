$('form').validate({
	rules: {
		name: {
			minlength: 3,
			maxlength: 255,
			required: true
		},
		description: {
			required: true
		},
		image: {
			required: function(element) {
					if($('h1').html() == 'New Product') {
						return true;
					} else {
						return false;
					}
				}
		},
		price: {
			digits: true,
			required: true
		},
		stock: {
			digits: true,
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