/** Exported with Rockmongo **/

/** system.users indexes **/
db.getCollection("system.users").ensureIndex({
  "_id": 1
},[
  
]);

/** tracking indexes **/
db.getCollection("tracking").ensureIndex({
  "_id": 1
},[
  
]);

/** tracking indexes **/
db.getCollection("tracking").ensureIndex({
  "service": 1
},[
  
]);

/** tracking indexes **/
db.getCollection("tracking").ensureIndex({
  "source": 1
},[
  
]);

/** tracking indexes **/
db.getCollection("tracking").ensureIndex({
  "extra": 1
},[
  
]);

/** tracking indexes **/
db.getCollection("tracking").ensureIndex({
  "timestamp": 1
},[
  
]);

/** system.indexes records **/
db.getCollection("system.indexes").insert({
  "v": 1,
  "key": {
    "_id": 1
  },
  "ns": "rssprocer.tracking",
  "name": "_id_"
});
db.getCollection("system.indexes").insert({
  "v": 1,
  "key": {
    "_id": 1
  },
  "ns": "rssprocer.system.users",
  "name": "_id_"
});
db.getCollection("system.indexes").insert({
  "v": 1,
  "key": {
    "service": 1
  },
  "ns": "rssprocer.tracking",
  "background": 1,
  "name": "idx-service"
});
db.getCollection("system.indexes").insert({
  "v": 1,
  "key": {
    "source": 1
  },
  "ns": "rssprocer.tracking",
  "name": "idx-source"
});
db.getCollection("system.indexes").insert({
  "v": 1,
  "key": {
    "extra": 1
  },
  "ns": "rssprocer.tracking",
  "name": "idx-extra"
});
db.getCollection("system.indexes").insert({
  "v": 1,
  "key": {
    "timestamp": 1
  },
  "ns": "rssprocer.tracking",
  "name": "idx-timestamp"
});

/** system.users records **/
db.getCollection("system.users").insert({
  "_id": ObjectId("4f6ff413836ebcc0c0d6687b"),
  "user": "root",
  "readOnly": false,
  "pwd": "2a8025f0885adad5a8ce0044070032b3"
});

/** tracking records **/
