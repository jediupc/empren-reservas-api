var Usuari = require('../models/usuari');
var jwt = require('jsonwebtoken');

exports.tokenSecret = "jkaLlUISADL3JgsajdniA66a3476dbkjKJSBAajsdhyiISUDYytasd";

exports.authenticate = function(req, res, next) {
    if (!req.body.username || !req.body.password) {
        res.status(401).json({codError: 401, descError: 'Falta nom usuari i/o constrasenya'});
    }
    else {
        var username = req.body.username;
        var password = req.body.password;
        Usuari.findOne({nom_usuari: username}, function(err, usuari) {
            if (err) { 
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'Error intern al servidor. Veure log'});
            }
            else if (!usuari || usuari.contrasenya !== password) {
                res.status(401).json({codError: 401, descError: 'Nom usuari i/o constrasenya és incorrecte'});
            }
            else {
                var token = jwt.sign(usuari, exports.tokenSecret, { expiresInMinutes: 120 });
                var data = {
                    usuari: usuari,
                    token: token
                };
                res.send(data);
            }
        });
    }
};