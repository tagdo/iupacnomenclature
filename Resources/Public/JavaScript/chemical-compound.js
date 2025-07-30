document.addEventListener('DOMContentLoaded', function () {
    const substituentsContainer = document.getElementById('substituents');
    let substituentIndex = 1;

    function createRemoveButton(parentDiv) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = 'Remove';
        btn.style.marginLeft = '10px';
        btn.style.backgroundColor = '#d9534f';
        btn.style.color = 'white';
        btn.style.border = 'none';
        btn.style.padding = '2px 8px';
        btn.style.cursor = 'pointer';
        btn.addEventListener('click', function () {
            parentDiv.remove();
        });
        return btn;
    }

    // Substituent
    document.getElementById('addSubstituentButton').addEventListener('click', function () {
        const div = document.createElement('div');
        div.className = 'substituent';
        div.innerHTML = `
            <input type="text" name="substituents[${substituentIndex}][name]" placeholder="Name of Substituent">
            <input type="text" name="substituents[${substituentIndex}][positions]" placeholder="Positions (e.g. 2,3,5)">
            <input type="number" name="substituents[${substituentIndex}][count]" placeholder="Count">
        `;
        div.appendChild(createRemoveButton(div));
        substituentsContainer.appendChild(div);
        substituentIndex++;
    });

    // Verzweigter Substituent
    document.getElementById('addBranchButton').addEventListener('click', function () {
        const div = document.createElement('div');
        div.className = 'substituent';
        div.innerHTML = `
            <input type="text" name="branchSubstituents[${substituentIndex}][branchName]" placeholder="Name of the substituent of the branched chain">
            <input type="text" name="branchSubstituents[${substituentIndex}][branchPositions]" placeholder="Positionof thes Substituent">
            <input type="number" name="branchSubstituents[${substituentIndex}][branchCount]" placeholder="Number of substituents">
            <input type="number" name="branchSubstituents[${substituentIndex}][branchLength]" placeholder="Length of the chain">
            <input type="number" name="branchSubstituents[${substituentIndex}][branchChainPosition]" placeholder="Position of the branched chain">
        `;
        div.appendChild(createRemoveButton(div));
        substituentsContainer.appendChild(div);
        substituentIndex++;
    });

    // Halogen
    document.getElementById('addHalogenButton').addEventListener('click', function () {
        const div = document.createElement('div');
        div.className = 'halogen-field';
        div.innerHTML = `
            <input type="text" name="halogens[${substituentIndex}][halogen]" placeholder="Halogen (e.g. F, Cl, Br)">
            <input type="text" name="halogens[${substituentIndex}][halogenPositions]" placeholder="Positions">
            <input type="number" name="halogens[${substituentIndex}][halogenCount]" placeholder="Number">
        `;
        div.appendChild(createRemoveButton(div));
        substituentsContainer.appendChild(div);
        substituentIndex++;
    });

    // Alkohol
    document.getElementById('addAlcoholButton').addEventListener('click', function () {
        const div = document.createElement('div');
        div.className = 'alcohol-field';
        div.innerHTML = `
            <label><input type="checkbox" name="isAlcohol" value="1">Contains OH group?</label>
            <input type="text" name="alcohols[${substituentIndex}][alcoholPositions]" placeholder="Position(s) (e.g. 2 or 2,3)">
            <input type="number" name="alcohols[${substituentIndex}][alcoholCount]" placeholder="Number">
        `;
        div.appendChild(createRemoveButton(div));
        substituentsContainer.appendChild(div);
        substituentIndex++;
    });

    // Cyclo (nur einmal, ohne Remove-Button)
    document.getElementById('addCycloButton').addEventListener('click', function () {
        const div = document.createElement('div');
        div.className = 'cyclo-field';
        div.innerHTML = `
            <label>
                <input type="checkbox" name="isCyclo" value="1">
                Is cyclic?
            </label>
        `;
        substituentsContainer.appendChild(div);
        this.disabled = true;
    });
});