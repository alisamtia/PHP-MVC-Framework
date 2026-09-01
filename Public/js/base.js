async function ajax(
  uri,
  formData,
  method,
  convert_to_json = true,
  content_type_orignal = null,
) {
  let content_type = null;
  let res_func = {};

  if (method == "POST" || method == "GET") {
  } else {
    // METHOD(PUT, PATCH ,DELETE) multi-inputs proccessing for json
    const data = {};
    formData =
      formData instanceof FormData
        ? formData.entries()
        : Object.entries(formData);

    for (const [key, value] of formData) {
      if (key.endsWith("[]")) {
        const cleanKey = key.slice(0, -2);

        if (!data[cleanKey]) {
          data[cleanKey] = [];
        }

        data[cleanKey].push(value);
      } else {
        data[key] = value;
      }
    }

    if (convert_to_json) {
      formData = JSON.stringify(data);

      content_type = "application/json";
    } else {
      formData = data;
    }
  }

  if (content_type_orignal) {
    content_type = content_type_orignal;
  }

  try {
    await fetch(uri, {
      method: method,
      body: formData,
      headers: content_type
        ? {
            "Content-Type": content_type,
          }
        : undefined,
    })
      .then((res) => {
        return res.text().then((text) => {
          if (!res.ok) {
            console.log("Response was not ok:", text);
            return null;
          }
          try {
            return JSON.parse(text);
          } catch (error) {
            console.error("Response was not valid JSON:", text);
            return text;
          }
        });
      })
      .then((data) => {
        res_func = data;
        if (!data?.success) {
          errors = data.errors;
          clear_errors();
          if (errors?.critical) {
            console.error(errors.critical);
            res_func = {
              success: false,
            };
            return;
          }
          Object.keys(errors).forEach((element) => {
            document.getElementById(`error-${element}`).innerHTML =
              errors[element];
          });
          res_func = {
            success: false,
          };
        }
      });
  } catch (error) {
    console.error("The response returned is not a valid json.");
    res_func = {
      success: false,
    };
  }
  return res_func;
}

function isFloat(value) {
  return typeof value === "number" && !Number.isInteger(value);
}

function error(msg) {
  return {
    errors: true,
    msg: msg,
  };
}

function success() {
  return {
    errors: false,
  };
}

function int_or_float_verify(id, min, max, field_name = "") {
  const element = document.getElementById(id).value.trim();

  if (element === "") {
    return error(`The ${field_name} is Required!`);
  }

  if (
    !isFloat(parseFloat(element, 10)) &&
    !Number.isInteger(parseInt(element))
  ) {
    return error(`The ${field_name} is not integer nor float!`);
  }

  if (parseFloat(element, 10) > max) {
    return error(`The ${field_name} must be less than ${max}!`);
  }

  if (parseFloat(element, 10) <= min) {
    return error(`The field ${field_name} must be atleast more than ${min}!`);
  }
  return success();
}

function str_verify(id, min, max, field_name) {
  const element = document.getElementById(id).value;

  if (element.trim() === "") {
    return error(`The field ${field_name} is Required!`);
  }

  if (element.trim().length > max) {
    return error(`The field ${field_name} must be less than ${max}!`);
  }
  if (element.trim().length < min) {
    return error(`The field ${field_name} must be longer than ${min}!`);
  }

  return success();
}

function multi_input_verify(class_name, min, max, field_name) {
  items = document.querySelectorAll(class_name);
  for (const item of items) {
    value_item = item.value.trim();
    if (value_item.length == 0) {
      return error(`Each ${field_name} is Required!`);
    }

    if (value_item.length > max) {
      return error(`Each ${field_name} must be less than ${max}!`);
    }
    if (value_item.length < min) {
      return error(`Each ${field_name} must be longer than ${min}!`);
    }
  }
  return success();
}

function fade_out_remove(item) {
  item.classList.add("bg-red-200");
  item.classList.add("opacity-0");
  item.addEventListener("transitionend", () => {
    item.remove();
  });
}
