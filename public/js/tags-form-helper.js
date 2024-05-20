function addCollectionAttribute() {
    const collectionHolder = document.querySelector('#tag-wrapper');
    const tag = document.createElement('div');
    tag.className = 'tag';

    tag.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );

    collectionHolder.appendChild(tag);

    collectionHolder.dataset.index++;

    addRemoveAttributeButton(tag);
}

function addRemoveAttributeButton(item) {
    const removeFormButton = document.createElement('a');
    removeFormButton.href = '#'
    removeFormButton.innerText = 'Delete tag';

    tag.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        tag.remove();
    });
}

document.addEventListener('DOMContentLoaded', () =>{
    document
        .querySelector('#add-tag')
        .addEventListener('click', (e) =>{
            e.preventDefault();

            addCollectionAttribute();
        })

    document
        .querySelectorAll('#tag-wrapper div.tag')
        .forEach((row)=>{
            addRemoveAttributeButton(row);
        })
})