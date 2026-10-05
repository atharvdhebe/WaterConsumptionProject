function validateConsumptionForm() {

    let family =
        document.getElementById("family_members").value;

    if (family <= 0) {

        alert(
            "Family members must be greater than zero."
        );

        return false;
    }

    return true;
}