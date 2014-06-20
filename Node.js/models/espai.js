var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var espaiSchema = new Schema ({
    codi: {type: String, unique: true},
    descripcio: String
});

module.exports = mongoose.model('Espai', espaiSchema, 'espais');

