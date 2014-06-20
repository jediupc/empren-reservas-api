var Usuari = require('../models/usuari');

exports.actionList = function(req, res) {
    if (userAuth.es_admin) {
        Usuari.find(req.query, function(err, usuaris) {
            if (err) {
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'LIST: Error intern al servidor. Veure log'});
            }
            else res.json(usuaris);
        });
    }
    else {
        res.json([userAuth]);
    }
}
 
exports.actionShow = function(req, res) {
    if (userAuth.es_admin) {
        Usuari.findById(req.params.id, function(err, usuari) {
            if (err) {
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'SHOW: Error intern al servidor. Veure log'});
            }
            else if (!usuari) res.status(404).json({codError: 404, descError: "SHOW: No existeix l'usuari amb id=" + req.params.id});
            else res.json(usuari);
        });
    }
    else {
        if (userAuth._id === req.params.id) res.json(userAuth);
        else res.status(403).json({codError: 403, descError: "SHOW: L'usuari no té permís per accedir a l'usuari amb id=" + req.params.id});
    }
}
 
exports.actionCreate = function(req, res) {
    if (!userAuth.es_admin) 
        res.status(403).json({codError: 403, descError: "CREATE: L'usuari no té permís per crear usuaris"});
    else {
        var usuari = new Usuari(req.body);
        usuari.save(function(err) {
            if (err) {
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'CREATE: Error intern al servidor. Veure log'});
            }
            else res.json({_id: usuari._id});
        });
    }
}
 
exports.actionUpdate = function(req, res) {
    if (!userAuth.es_admin) 
        res.status(403).json({codError: 403, descError: "UPDATE: L'usuari no té permís per actualitzar usuaris"});
    else {
        Usuari.findById(req.params.id, function(err, usuari) {
            if (err) {
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'UPDATE: Error intern al servidor. Veure log'});
            }
            else if (!usuari) res.status(404).json({codError: 404, descError: "UPDATE: No existeix l'usuari amb id=" + req.params.id});
            else {
                usuari.set(req.body);
                usuari.save(function(err) {
                    if (err) {
                        console.error(new Date().toISOString(), err);
                        res.status(500).json({codError: 500, descError: 'UPDATE: Error intern al servidor. Veure log'});
                    }
                    else res.json({_id: usuari._id});
                });   
            }
        });
    }
}
 
exports.actionDelete = function(req, res) {
    if (!userAuth.es_admin) 
        res.status(403).json({codError: 403, descError: "DELETE: L'usuari no té permís per esborrar usuaris"});
    else {
        Usuari.findById(req.params.id, function(err, usuari) {
            if (err) { 
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'DELETE: Error intern al servidor. Veure log'});
            }
            else if (!usuari) res.status(404).json({codError: 404, descError: "DELETE: No existeix l'usuari amb id=" + req.params.id});
            else {
                usuari.remove(function(err) {
                    if (err) { 
                        console.error(new Date().toISOString(), err);
                        res.status(500).json({codError: 500, descError: 'DELETE: Error intern al servidor. Veure log'});
                    }
                    else res.json({_id: usuari.id});
                });
            }              
        });
    }
}

