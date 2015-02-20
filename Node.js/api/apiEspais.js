var Espai = require('../models/espai');

exports.actionList = function(req, res) {

    Espai.find(req.query, function(err, espais) {
        if (err) {
            console.error(new Date().toISOString(), err);
            res.status(500).json({codError: 500, descError: 'LIST: Error intern al servidor. Veure log'});
        }
        else res.json(espais);
    });

};
 
exports.actionShow = function(req, res) {
    Espai.findById(req.params.id, function(err, espai) {
        if (err) {
            console.error(new Date().toISOString(), err);
            res.status(500).json({codError: 500, descError: 'SHOW: Error intern al servidor. Veure log'});
        }
        else if (!espai) res.status(404).json({codError: 404, descError: "SHOW: No existeix l'espai amb id=" + req.params.id});
        else res.json(espai);
    });
};
 
exports.actionCreate = function(req, res) {
    if (!req.user.es_admin)
        res.status(403).json({codError: 403, descError: "CREATE: L'usuari no té permís per crear espais"});
    else {
        var espai = new Espai(req.body);
        espai.save(function(err) {
            if (err) {
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'CREATE: Error intern al servidor. Veure log'});
            }
            else res.json({_id: espai._id});
        });
    }
};
 
exports.actionUpdate = function(req, res) {
    if (!req.user.es_admin)
        res.status(403).json({codError: 403, descError: "UPDATE: L'usuari no té permís per actualitzar espais"});
    else {
        Espai.findById(req.params.id, function(err, espai) {
            if (err) {
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'UPDATE: Error intern al servidor. Veure log'});
            }
            else if (!espai) res.status(404).json({codError: 404, descError: "UPDATE: No existeix l'espai amb id=" + req.params.id});
            else {
                espai.set(req.body);
                espai.save(function(err) {
                    if (err) {
                        console.error(new Date().toISOString(), err);
                        res.status(500).json({codError: 500, descError: 'UPDATE: Error intern al servidor. Veure log'});
                    }
                    else res.json({_id: espai._id});
                });   
            }
        });
    }
};
 
exports.actionDelete = function(req, res) {
    if (!req.user.es_admin)
        res.status(403).json({codError: 403, descError: "DELETE: L'usuari no té permís per esborrar espais"});
    else {
        Espai.findById(req.params.id, function(err, espai) {
            if (err) { 
                console.error(new Date().toISOString(), err);
                res.status(500).json({codError: 500, descError: 'DELETE: Error intern al servidor. Veure log'});
            }
            else if (!espai) res.status(404).json({codError: 404, descError: "DELETE: No existeix l'espai amb id=" + req.params.id});
            else {
                espai.remove(function(err) {
                    if (err) { 
                        console.error(new Date().toISOString(), err);
                        res.status(500).json({codError: 500, descError: 'DELETE: Error intern al servidor. Veure log'});
                    }
                    else res.json({_id: espai._id});
                });
            }              
        });
    }
};