// ---------- Requires
var express = require('express');
var https = require('https');
var fs = require('fs');
var bodyParser = require('body-parser');
var mongoose = require('mongoose');
var apiEspais = require('./api/apiEspais');
var apiUsuaris = require('./api/apiUsuaris');
var apiReserves = require('./api/apiReserves');
var auth = require('./utils/authentication');

var app = express();
app.use(bodyParser());

// ------------ Errors
app.use(function(err, req, res, next) {
    if (err) {
        console.error(new Date().toISOString(), err);
        res.status(500).json({codError: 500, descError: 'Error intern al servidor. Veure log'});
    }
    else next(); 
});

// -------------- Base de Dades
mongoose.connect('mongodb://user_reserves:pass_reserves@localhost/reserves', function(err) {
    if (err) {
        console.log(new Date().toISOString(), 'Error de connexió a Base de Dades. Veure log');
        console.error(new Date().toISOString(), err);
        process.exit(1);
    }
    else console.log(new Date().toISOString(), 'Connectat a Base de Dades');
});

mongoose.connection.on('error', function(err) {
    console.log(new Date().toISOString(), 'Error de Base de Dades. Veure log');
    console.error(new Date().toISOString(), err);
});

// ------------ Routes
var router = express.Router();

router.use(function(req, res, next) {
    auth.authenticate(req, res, next);
});

router.route('/espais')
    .get(apiEspais.actionList)
    .post(apiEspais.actionCreate);
router.route('/espais/:id')
    .get(apiEspais.actionShow)
    .put(apiEspais.actionUpdate)
    .delete(apiEspais.actionDelete);

router.route('/usuaris')
    .get(apiUsuaris.actionList)
    .post(apiUsuaris.actionCreate);
router.route('/usuaris/:id')
    .get(apiUsuaris.actionShow)
    .put(apiUsuaris.actionUpdate)
    .delete(apiUsuaris.actionDelete);

router.route('/reserves')
    .get(apiReserves.actionList)
    .post(apiReserves.actionCreate);
router.route('/reserves/:id')
    .get(apiReserves.actionShow)
    .put(apiReserves.actionUpdate)
    .delete(apiReserves.actionDelete);

app.use('/api', router);

// ---------- Server
var server = https.createServer({
    key: fs.readFileSync('./https/key.pem'), 
    cert: fs.readFileSync('./https/cert.pem')
}, app);

var port = process.env.PORT || 8080;
//app.listen(port); //--> HTTP
server.listen(port); // --> HTTPS 
console.log(new Date().toISOString(), 'API Reserves, port ' + port);