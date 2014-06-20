var Usuari = require('../models/usuari');

userAuth = null;

exports.authenticate = function(req, res, next) {
    if (!req.headers.x_username || !req.headers.x_password) {
        res.status(401).json({codError: 401, descError: 'Falta nom usuari i/o constrasenya'});
    }
    else {
        var username = req.headers.x_username;
        var password = req.headers.x_password;
        Usuari.findOne({nom_usuari: username}, function(err, usuari) {
            if (err) { 
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'Error intern al servidor. Veure log'});
            }
            else if (!usuari || usuari.contrasenya !== password) {
                res.status(401).json({codError: 401, descError: 'Nom usuari i/o constrasenya és incorrecte'});
            }
            else {
                userAuth = usuari;
                next();
            }
        });
    }
};