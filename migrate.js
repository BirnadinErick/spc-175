import mysql from 'mysql';
import fs from 'fs';
import path from 'path';

import data_rs from "./contents.json" assert {type: 'json'};

var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'spc',
  password : 'spcmediaunit2023',
  database : 'spc'
});
 
connection.connect();
 
connection.query('SELECT * FROM contents', function (error, results, fields) {
  if (error) throw error;
});
 

let rows = data_rs.data.map((r,i)=> construct_query(r.path, r.data, i));

rows.map((q,i) => connection.query(q, function(e,r,f) {
    if (e) throw e;

    console.log(`${i}-row TRUE`);
}));

connection.end();


function construct_query(path, data, i) {
    const uid = `spc_media_unit_668b9bace0c871.4203801${i}`;
    const q = `INSERT INTO contents (path, uid, updated_by, data, meta, updated_at) VALUES
        ("${path}", "${uid}", 6, "${data}", "", "2024-07-08")`;
    return q;
}
