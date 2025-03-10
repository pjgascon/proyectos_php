<?php
// $uri = "https://countries.trevorblades.com/";
// $array = ['query' => 'query{
//         continents(filter: { code: { eq: "AF" } }){
//             name
//         }
//     }'];

// $data = http_build_query($array);
// $iniciar = curl_init();

// curl_setopt($iniciar, CURLOPT_URL, $uri);
// curl_setopt($iniciar, CURLOPT_POST, true);
// // curl_setopt($iniciar, CURLOPT_HTTPHEADER, $array_cabecera);
// curl_setopt($iniciar, CURLOPT_POSTFIELDS, $data);

// $respuesta = curl_exec($iniciar);

// if (curl_errno($iniciar)) echo curl_error($iniciar);
// else $resp = json_decode($respuesta, true);

// foreach ($resp as $obj => $valor) {
//     echo "$obj : $valor";
// }

// curl_close($iniciar);

$r = shell_exec("curl 'https://test.infowork.es:8081/graphql' -H 'Accept-Encoding: gzip, deflate, br' -H 'Content-Type: application/json' -H 'Accept: application/json' -H 'Connection: keep-alive' -H 'DNT: 1' -H 'Origin: https://test.infowork.es:8081' -H 'Authorization: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkYXRhIjp7IklkIjoxMzQzMCwiQ29kaWdvIjo4NTgwLCJUaXBvIjoiQyIsIk5JRiI6IkI5MTg5MzM3MCIsIk5vbWJyZSI6IkNPTk5FQ1QgR09MRCBTLkwuIFdTIiwiTm9tYnJlQ29udGFjdG8iOiJNQU5VRUwgLyBBUkFDRUxJIiwiSWRBY3RpdmlkYWQiOjE3OTAsIklkVGFyaWZhIjoyMzc0LCJJZENsaWVudGVHcnVwbyI6NjAzLCJDcmVkaXRvIjpudWxsLCJDcmVkaXRvQ29uc3VtaWRvIjowLCJFc0Jsb3F1ZWFkbyI6ZmFsc2UsIlByaXZpbGVnaW9zUk1BIjpmYWxzZSwiUmVjYXJnb0VxdWl2YWxlbmNpYSI6ZmFsc2UsIkZvcm1hUGFnbyI6IlRyYW5zZmVyZW5jaWEgUFJFUEFHTyIsIklCQU4iOm51bGwsIkNvbnRyYXNlbnlhIjoiTHBtQ29ubmUiLCJJZEFnZW50ZSI6MzcsIkZlY2hhQWx0YSI6IjIwMTYtMTItMTRUMjM6MDA6MDAuMDAwWiIsIkZlY2hhTW9kaWZpY2FjaW9uIjoiMjAyMy0wNy0wN1QwODoxNjowMC4wMDBaIiwiSVNQIjp0cnVlLCJEb2N1bWVudG9JU1AiOmZhbHNlLCJPYmpldGl2b3MiOmZhbHNlLCJFc0Jsb3F1ZWFkb1BlZGlkb1dlYiI6ZmFsc2UsIkltcGVkaXJEcm9wc2hpcHBpbmciOmZhbHNlLCJFeGNsdWlkb0Nhbm9uIjpmYWxzZSwiRXhjbHVpZG9Qcm9tb2Npb25lc1dlYiI6ZmFsc2UsIl9fdHlwZW5hbWUiOiJDbGllbnRlIn0sImlhdCI6MTY4OTkyMDMxMCwiZXhwIjoxNjkwMDA2NzEwfQ.SLbVO6vAMukF_AKFjP3xqE1JT0caTMpzXIxNhszoD6w' --data-binary '{\"query\":\"query{ catalogo{ categoriaListado{ numeroDeRegistros fecha resultado datos{ id nombre idCategoriaPadre codigo } } } } \"}' --compressed");
var_dump($r);
