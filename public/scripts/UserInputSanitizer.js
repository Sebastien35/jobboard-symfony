function userInputSanitizer(input) {
  return  input.replace(/[^a-zA-Z0-9]/g, '');
}