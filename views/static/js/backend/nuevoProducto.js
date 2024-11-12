

/*< carga inicial de elementos en el listado*/
// loadPage();

async function addNewProduct(btn) {
	let prod_name = document.querySelector("#prod_name").value;
	let prod_desc = document.querySelector("#prod_desc").value;
	let prod_unit_price = document.querySelector("#prod_unit_price").value;
	let prod_stock = document.querySelector("#prod_stock").value;

	const uploadForm = document.getElementById('uploadForm');
	const formData = new FormData(uploadForm);

	try {
		const uploadResponse = await fetch('api/producto/saveFile', {
			method: 'POST',
			body: formData,
		});

		const uploadData = await uploadResponse.json();
		console.log(uploadData); // Para depuración

		document.getElementById('response').innerHTML = uploadData.list.error;

		if (uploadData.list.errno === 200) {
			const url = `api/producto/addNewProduct`;
			const data = {
				name: prod_name,
				desc: prod_desc,
				unit_price: prod_unit_price,
				stock: prod_stock,
				url_image: uploadData.list.name // Asegúrate de que esto sea correcto
			};

			const response = await fetch(url, {
				method: "POST",
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(data)
			});

			if (!response.ok) {
				console.error("Error al agregar el producto", response);
			} else {
				const result = await response.json();
				console.log("Producto agregado:", result); // Para depuración
			}
		}
	} catch (error) {
		console.error("Error en la operación:", error);
	}

	console.log("Fin");
}
