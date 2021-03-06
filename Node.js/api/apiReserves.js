var Reserva = require('../models/reserva');
var Espai = require('../models/espai');
var Usuari = require('../models/usuari');

var mongoose = require('mongoose');

var url = require('url');



exports.actionList = function (req, res) {
  var filter = makeFilter(req);
  Reserva.find(filter).populate('espai').populate('usuari').exec(function (err, reserves) {
    if (err) {
      console.error(new Date().toISOString(), err);
      res.status(500).json({codError: 500, descError: 'LIST: Error intern al servidor. Veure log'});
    }
    else {
      /*if (!req.user.es_admin) { // Si no es admin, nomes enviem el propi usuari
        for (var i = 0, len = reserves.length; i < len; i++) {
          if (!mongoose.Types.ObjectId(req.user._id).equals(reserves[i].usuari._id)) reserves[i].usuari = null;
        }
      }*/
      res.json(reserves);
    }
  });
};

exports.actionShow = function (req, res) {
  Reserva.findById(req.params.id).populate('espai').populate('usuari').exec(function (err, reserva) {
    if (err) {
      console.error(new Date().toISOString(), err);
      res.status(500).json({codError: 500, descError: 'SHOW: Error intern al servidor. Veure log'});
    }
    else if (!reserva) res.status(404).json({
      codError: 404,
      descError: "SHOW: No existeix la reserva amb id=" + req.params.id
    });
    else {
      if (!req.user.es_admin && !req.user._id.equals(reserva.usuari._id)) reserva.usuari = null;
      reserva.data_hora = reserva.data_hora.toString();
      res.json(reserva);
    }
  });
};

exports.actionCreate = function (req, res) {
  var reserva = new Reserva(req.body);
  reserva.data_hora = parseDate(req.body.data_hora);
  if (!req.user.es_admin || !reserva.usuari) reserva.usuari = req.user._id;
  reserva.data_modif = new Date();
  reserva.save(function (err) {
    if (err) {
      if (err.code == 11000) {
        res.status(490).json({codError: 490, descError: 'CREATE: La reserva aquest dia a aquesta hora ja existeix'});
      } else {
        res.status(500).json({codError: 500, descError: 'SHOW: Error intern al servidor. Veure log'});
        res.status(500).json({codError: 500, descError: 'SHOW: Error intern al servidor. Veure log'});
      }
    }
    else res.status(200).json({_id: reserva._id});
  });
};

exports.actionUpdate = function (req, res) {
  if (!req.user.es_admin)
    res.status(403).json({codError: 403, descError: "UPDATE: L'usuari no té permís per actualitzar reserves"});
  else {
    Reserva.findById(req.params.id, function (err, reserva) {
      if (err) {
        console.error(new Date().toISOString(), err);
        res.status(500).json({codError: 500, descError: 'UPDATE: Error intern al servidor. Veure log'});
      }
      else if (!reserva) res.status(404).json({
        codError: 404,
        descError: "UPDATE: No existeix la reserva amb id=" + req.params.id
      });
      else {
        reserva.set(req.body);
        if (req.body.data_hora) reserva.data_hora = parseDate(req.body.data_hora);
        reserva.data_modif = new Date();
        reserva.save(function (err) {
          if (err) {
            console.error(new Date().toISOString(), err);
            res.status(500).json({codError: 500, descError: 'UPDATE: Error intern al servidor. Veure log'});
          }
          else res.json({_id: reserva._id});
        });
      }
    });
  }
};

exports.actionDelete = function (req, res) {
  Reserva.findById(req.params.id, function (err, reserva) {
    if (err) {
      console.error(new Date().toISOString(), err);
      res.status(500).json({codError: 500, descError: 'DELETE: Error intern al servidor. Veure log'});
    }
    else if (!reserva) res.status(404).json({
      codError: 404,
      descError: "DELETE: No existeix la reserva amb id=" + req.params.id
    });
    else {
      if (req.user.es_admin || mongoose.Types.ObjectId(req.user._id).equals(reserva.usuari)) {
        reserva.remove(function (err) {
          if (err) {
            console.error(new Date().toISOString(), err);
            res.status(500).json({codError: 500, descError: 'DELETE: Error intern al servidor. Veure log'});
          }
          else res.json({_id: reserva._id});
        });
      }
      else res.status(403).json({
        codError: 403,
        descError: "DELETE: L'usuari no té permís per esborrar la reserva amb id=" + req.params.id
      });
    }
  });
};

// --------------------- Funcions auxiliars
function makeFilter(req) {

  var url_parts = url.parse(req.url, true);
  var query = url_parts.query;
  //var query = req.query;
  var result = {};
  if (query.espai) result.espai = query.espai;
  if (req.user.es_admin && query.usuari) result.usuari = query.usuari;
  if (query.inici && query.fi)
    result.data_hora = {$gte: parseDate(query.inici), $lte: parseDate(query.fi)};
  else if (query.inici)
    result.data_hora = {$gte: parseDate(query.inici)};
  else if (query.fi)
    result.data_hora = {$lte: parseDate(query.fi)};
  return result;
}

function parseDate(text) {
  var year = parseInt(text.substr(0, 4));
  var month = parseInt(text.substr(4, 2)) - 1;
  var day = parseInt(text.substr(6, 2));
  var hour = (text.length >= 'yyyymmddhh'.length) ? parseInt(text.substr(8, 2)) : 0;
  var min = (text.length >= 'yyyymmddhhmi'.length) ? parseInt(text.substr(10, 2)) : 0;
  var sec = (text.length >= 'yyyymmddhhmiss'.length) ? parseInt(text.substr(12, 2)) : 0;
  return new Date(Date.UTC(year, month, day, hour, min, sec));
}