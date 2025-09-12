let inputValidations = {
    isString: (str) => {
        return str;
    },
    isNumber: (str) => {
        return /^\d+$/.test(str);  // returns a boolean
    },
    isNotEmpty: (str) => {
        return /\S+/.test(str);  // returns a boolean
    },
    isEmail: (email) => {
        const pattern = /^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,3}$/;
        return pattern.test(email);
    },
    isEmailValid: (event, id) => {
        const pattern = /^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,3}$/;
        return pattern.test($(id).val()) ? $(id).val() : $(id).val('');
    },
    isPasswordValid: (event, id) => {
        let pattern = /^(?=.*\d)(?=.*[!-\/:-@\[-`{-~]).{8,}$/
        return pattern.test($(id).val()) ? $(id).val() : $(id).val('');
    },
    isMatched: (firstId, currentId, validationMsg) => {
        if ($(firstId).val() == '' && $(currentId).val() == '') return $(validationMsg).html('').css('color', 'red');
        let isMatched = $(firstId).val() === $(currentId).val();
        if (isMatched) $(validationMsg).html('Matched').css('color', 'green');
        else $(validationMsg).html('Not Matching').css('color', 'red');
    },
    isReason: (str) => {
        let pattern = /^[ء-يa-zA-Z0-9]{4,32}$/;
        return pattern.test(str)
    },
    numbersOnly: (event) => {
        let k;
        document.all ? k = event.keyCode : k = event.which;
        return (k == 8 || (k >= 48 && k <= 57));
    },
    alphabetsOnly: (event) => {
        let alphaOnlyRegex = new RegExp("([a-zA-Zء-ي]+)"); 
        return alphaOnlyRegex.test(event.key);
    },
    isValidLength: (str) => {
        return /^[ء-يa-zA-Z0-9 .,]{4,200}$/.test(str);
    },
    noteLengthValidation: (id) => {
        let noteLength = /^[ء-يa-zA-Z0-9 .,]{1,200}$/;
        return $(id).val().length <= 200 ? $(id).val() : $(id).val('');
    },
    maxNumber: (max, id = '#licenses') => {
        let currentValue = Number($(id).val());
        if (currentValue && currentValue > max) return $(id).val('');
        return true;
    },
    userNameValidate: (event, id) => {
        let key = event.keyCode;
        let pattern = /^[ء-يa-zA-Z0-9_.@]+$/;
        return pattern.test($(id).val()) ? $(id).val() : $(id).val('');
    },
    userNameValidateWithMail: (event, id) => {
        let key = event.keyCode;
        let pattern = /^[ء-يa-zA-Z0-9_.@]{1,30}$/;
        return pattern.test($(id).val()) ? $(id).val() : $(id).val('');
    }
};
