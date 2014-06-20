var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var usuariSchema = new Schema ({
    nom_usuari: {type: String, unique: true},
    contrasenya: String,
    nom: String,
    cognoms: String,
    es_admin: Boolean,
    espais: [{type: Schema.Types.ObjectId, ref: 'Espai'}]
});

module.exports = mongoose.model('Usuari', usuariSchema, 'usuaris');

