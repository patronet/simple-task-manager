import React from 'react';
import ReactDOM from 'react-dom';
import StmApp from './StmApp';
import './index.css';

// XXX
const serviceUrl = "/service.php";

ReactDOM.render(<StmApp serviceUrl={serviceUrl} />, document.getElementById('root'));

