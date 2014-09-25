// ---------- Requires
var express = require('express');
var https = require('https');
var http = require('http');
var fs = require('fs');
var bodyParser = require('body-parser');
var mongoose = require('mongoose');
var apiEspais = require('./api/apiEspais');
var apiUsuaris = require('./api/apiUsuaris');
var apiReserves = require('./api/apiReserves');
var auth = require('./utils/authentication');

var app = express();
app.use(bodyParser());

var allowCrossDomain = function(req, res, next) {
  res.header('Access-Control-Allow-Origin', '*');
  res.header('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS');
  res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Content-Length, X-Requested-With, X_USERNAME, X_PASSWORD');

  // intercept OPTIONS method
  if ('OPTIONS' == req.method) {
    res.send(200);
  }
  else {
    next();
  }
};

app.use(allowCrossDomain);

// ------------ Errors
app.use(function(err, req, res, next) {
    if (err) {
        console.error(new Date().toISOString(), err);
        res.status(500).json({codError: 500, descError: 'Error intern al servidor. Veure log'});
    }
    else next(); 
});

app.all('*', function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "X-Requested-With");
  next();
});

// -------------- Base de Dades
mongoose.connect('mongodb://localhost/reservas', function(err) {
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

/*http.createServer(app).listen(app.get('port'), function () {
  console.log('Express server listening on port ' + app.get('port'));
});*/

var port = process.env.PORT || 8080;
app.listen(port); //--> HTTP
//server.listen(port); // --> HTTPS
console.log(new Date().toISOString(), 'API Reserves, port ' + port);