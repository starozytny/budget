function updateInArray(array, data){
    let arr = []
    array.map(elem => {
        if(elem.id == data.id){ elem = data }
        arr.push(elem)
    })
    return arr;
}

function addInArray(array, data){
    let arr = []; 
    let obj = JSON.parse(data)
    array.forEach(element => {
        arr.push(element)
    });
    arr.push(obj) // if no find doc in docs => add new doc
    return arr;
}


function addOrUpdateInArray(array, data){
    let arr = []; let findOne = false;
    let obj = JSON.parse(data)
    array.forEach(element => {
        if(element.id == obj.id){
            element = obj; findOne = true // update documentation
        }
        arr.push(element)
    });
    if(!findOne) { arr.push(obj) } // if no find doc in docs => add new doc
    return arr;
}

function updateInArraySeen(array, id){
    let arr = []
    array.map(elem => {
        if(elem.id == id){ elem.isSeen = true }
        arr.push(elem)
    })
    return arr;
}

function deleteInArray(array, data){
    let arr = []
    array.map(elem => {
        if(elem.id != data.id){ arr.push(elem) }
    })
    return arr;
}

module.exports = {
    updateInArray,
    updateInArraySeen,
    deleteInArray,
    addOrUpdateInArray,
    addInArray
}