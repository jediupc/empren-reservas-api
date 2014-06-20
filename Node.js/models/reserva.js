var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var reservaSchema = new Schema ({
    data_hora: Date,
    espai: {type: Schema.Types.ObjectId, ref: 'Espai'},
    usuari: {type: Schema.Types.ObjectId, ref: 'Usuari'},
    data_modif: Date
});

reservaSchema.index({data_hora: 1, espai: 1}, {unique: true});

module.exports = mongoose.model('Reserva', reservaSchema, 'reserves');

