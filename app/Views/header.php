<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?=$title?></title>
  <style>
    body {
      font-family: sans-serif;
      background: #f9f9f9;
      padding: 40px;
      display: flex;
      justify-content: center;
    }
    .container {
      background: white;
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
    }
    h1 {
      margin-bottom: 20px;
      font-size: 24px;
      color: #333;
    }
    ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    li {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }
    li:last-child {
      border-bottom: none;
    }
    .left {
      display: flex;
      align-items: center;
      flex: 1;
      cursor: pointer;
    }
    input[type="checkbox"] {
      margin-right: 10px;
      transform: scale(1.2);
    }
    .completed {
      color: #aaa;
      text-decoration: line-through;
    }
    .delete-btn {
      background: none;
      border: none;
      color: #999;
      font-size: 16px;
      cursor: pointer;
      padding-left: 10px;
      transition: color 0.2s;
    }
    .delete-btn:hover {
      color: #e00;
    }
    .toggle-btn {
      margin-top: 20px;
      font-size: 14px;
      color: #666;
      background: none;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
    }
  </style>
</head>
<body>

