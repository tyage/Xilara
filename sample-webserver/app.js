const express = require('express');
const bodyParser = require('body-parser');

const app = express();

const todos = [];

app.use(bodyParser.urlencoded({
  extended: true
}));
app.set('views', `${__dirname}/views`);
app.set('view engine', 'ejs');

app.get('/', (req, res) => {
  res.render('index', { todos });
});

app.post('/', (req, res) => {
  todos.push(req.body.todo);
  res.redirect('/');
});

app.listen(3000, () => {
});
