import React, { useMemo, useState, useEffect } from 'react';
import axios from 'axios';
import { useTable, usePagination, useFilters, useExpanded } from 'react-table';
import 'bootstrap/dist/css/bootstrap.min.css';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faShoppingCart } from '@fortawesome/free-solid-svg-icons';
import { Link, withRouter } from 'react-router-dom';
import { withAlert } from 'react-alert';
import LunchKitItems from './LunchKitItems';
import { DomainDisabled } from '@material-ui/icons';
import CartModal from './CartModal';

const MixOrderItems = withAlert(({ history, location, alert }) => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [selectAll, setSelectAll] = useState(false);
    const [selectedRows, setSelectedRows] = useState([]);
    const [firstPageIndex, setFirstPageIndex] = useState(0);
    const [filterCategory, setFilterCategory] = useState('');
    const [filterName, setFilterName] = useState('');
    const [cartItems, setCartItems] = useState([]);
    const [cartCount, setCartCount] = useState();
    const [orderData, setOrderData] = useState([]);
    const [enableBtn, setEnableBtn] = useState(true);
    const [total, setTotal] = useState(0);
    const [originalTotal, setOriginalTotal] = useState(0);
    const [advance, setAdvance] = useState(0);
    const [cakePrice, setCakePrice] = useState(0);


    const [showCartModal, setShowCartModal] = useState(false); // State for showing/hiding the cart modal

        // Function to add item to cart
        const addToCart = (item) => {
            setCartItems(prevCartItems => [...prevCartItems, item]);
            setCartCount(prevCount => prevCount + 1);
        };
    
        // Function to remove item from cart
        const removeFromCart = (item) => {
            setCartItems(prevCartItems => prevCartItems.filter(cartItem => cartItem.item_id !== item.item_id));
            setCartCount(prevCount => prevCount - 1);

            
    // Uncheck the row corresponding to the removed item
    setSelectedRows(prevSelectedRows =>
        prevSelectedRows.filter(selectedRow => selectedRow.item_id !== item.item_id)
    );

    // Update the total price by subtracting the price of the removed item
    setTotal(prevTotal =>
        prevTotal - (parseInt(item.unit_price) * parseInt(item.receiving_quantity))
    );
        };
    
        // Function to handle closing the cart modal
        const handleCloseCartModal = () => {
            setShowCartModal(false);
        };
    
        // JSX for displaying the cart button
        const cartButton = (
            <button
                className="btn btn-primary btn-sm"
                onClick={() => setShowCartModal(true)}
                type="button"
            >
                <FontAwesomeIcon icon={faShoppingCart} /> {`(${cartCount})`}
            </button>
        );

    // const handleAdvance = (e) => {
    //     const enteredAdvance = parseFloat(e.target.value);
    //     if (!isNaN(enteredAdvance)) {
    //         setAdvance(enteredAdvance);
    //         const newTotal = total - parseFloat(enteredAdvance).toFixed(2);
    //         setTotal(enteredAdvance > 0 ? parseFloat(newTotal.toFixed(2)) : originalTotal);
    //     } else {
    //         // If entered advance is not a valid number, reset advance to 0
    //         setAdvance(0);
    //     }
    // };



    // const handleAdvance = (e) => {
    //     const enteredAdvance = parseFloat(e.target.value);
    //     if (!isNaN(enteredAdvance)) {
    //         setAdvance(enteredAdvance);
    //         if (enteredAdvance == total) {
    //             setTotal(originalTotal);
    //         }
    //         else {
    //             var newtotal = total - parseFloat(enteredAdvance).toFixed(2);
    //             console.log(newtotal);
    //         }
    //         // const newTotal = enteredAdvance === total ? originalTotal : total - enteredAdvance;
    //         // setTotal(parseFloat(newTotal.toFixed(2)));
    //     } else {
    //         // If entered advance is not a valid number, reset advance to 0
    //         setAdvance(0);
    //     }
    // };
    const handleAdvance = (e) => {
        const advanceValue = e.target.value.trim();
        if (advanceValue === "") {
            setAdvance('');
            setTotal(total);
            return ;
        }

        setAdvance(e.target.value);
        const x = parseFloat(e.target.value).toFixed(2);

        if (parseFloat(x) > parseFloat(total)) {
            setTotal(total);
            setAdvance(0);
        }
        // else if (parseFloat(x) > parseFloat(total)) {
        //     alert.error("Please Enter Valid Advance");
        //     setTotal(originalTotal);
        // }
        // else if (parseFloat(x) < 0) {
        //     alert.error("Please Enter Valid Advance");
        //     setTotal(originalTotal);
        // }

        // else {
        //     const newtotal = parseFloat(total) - parseFloat(x);

        //     setTotal(parseFloat(newtotal).toFixed(2));
        // }
        // else if (x > total) {
        //     setTotal(originalTotal);
        // }
        // else {
        //     const newtotal = originalTotal - x;

        //     setTotal(newtotal);
        // }
        // setTotal(e.target.value);
    }


    // useEffect(() => {
    //     setTotal(advance);
    // }, [advance]);

    // const [total, setTotal] = useState(() => {
    //     if (orderData.length > 0) {
    //         return calculatePrice();
    //     }
    //     return 0;
    // });

    useEffect(() => {
        window.scrollTo(0, 0);

    }, []);
    // useEffect(() => {
    //     if (orderData.length > 0 && cartItems.length > 0) {
    //         var totalPrice = 0;
    //         cartItems.forEach(function (item) {
    //             totalPrice += item.price;
    //         });
    //         setTotal((prev) => {
    //             return prev + calculatePrice() + totalPrice;
    //         })
    //         setEnableBtn(false);
    //     }
    // }, [cartItems]);
    // useEffect(() => {
    //     if (orderData.length > 0 && cartItems.length > 0) {
    //         // Calculate total price
    //         var totalPrice = 0;
    //         cartItems.forEach(function (item) {
    //             totalPrice += item.cost_price;
    //         });

    //         // Update total state
    //         setTotal((prevTotal) => {
    //             return calculatePrice() + totalPrice;
    //         });

    //         // Disable button
    //         setEnableBtn(false);
    //     }
    // }, [cartItems, orderData]);

    const calculatePrice = () => {
        const { productPrice, productWeight, productQuantity, selectedFlavours, selectedMaterials } = orderData[0];
        let totalPrice = 0;
        let flavorPrice = 0;
        //(weight*[flavours]+weight[materials]+productPrice)*quantity
        //selectedFlavours.forEach((flavour) => { totalPrice = totalPrice + (productWeight * flavour.flavourPrice) });
        if (selectedFlavours.length > 0) {
            selectedFlavours.forEach((flavour) => { flavorPrice = flavorPrice + flavour.flavourPrice * 1 });
            flavorPrice = (flavorPrice / selectedFlavours.length) * productWeight;
        }
        //selectedMaterials.forEach((material) => { totalPrice = totalPrice + (productWeight * material.materialPrice) });
        totalPrice = (totalPrice + (productPrice * 1 + flavorPrice * 1)) * productQuantity; //1* productPrice is important to convert "productPrice" to int
        return totalPrice;
    }
    useEffect(() => {
        if (orderData.length > 0 || cartItems.length > 0) {


            // Disable button
            setEnableBtn(false);

        }
    }, [cartItems, orderData]);

    useEffect(() => {
        if (location.state && location.state.cartData.length > 0) {
            const newCartData = location.state.cartData;
            console.log(newCartData);
            setOrderData(newCartData);

            setCartCount(newCartData.length);

        }
        else {
            setCartCount(0);
        }
    }, [location.state]);

    useEffect(() => {

        const fetchData = async () => {
            try {
                const response = await axios.get('/positems');
                const rdata = response.data.items.map(item => ({
                    ...item,
                   receiving_quantity: 1 // Assuming 'receiving_quantity' is the receiving_quantity value in the response
                    // receiving_quantity: item.receiving_quantity // Assuming 'receiving_quantity' is the receiving_quantity value in the response
                }));
                setData(rdata);
                setLoading(false);
            } catch (error) {
                console.error('Error fetching data:', error);
                setLoading(false);
            }
        };

        fetchData();
    }, []);


    useEffect(() => {
        setSelectedRows((prevSelectedRows) =>
            prevSelectedRows.filter((row) =>
                data.some((item) => item.item_id === row.item_id)
            )
        );
        setCartItems((prevCartItems) =>
            prevCartItems.filter((item) =>
                data.some((rowData) => rowData.item_id === item.item_id)
            )
        );
        // setCartCount(cartItems.length);
        // setCartCount((prev) => {
        //   return prev + cartItems.length
        // });
    }, []);

    const handleSelectAll = (e) => {
        const checked = e.target.checked;
        setSelectAll(checked);
        if (checked) {
            // Add all rows to selectedRows
            setSelectedRows([...filteredData]);
            // Add all rows to cartItems
            setCartItems([...filteredData]);
            // Update cart count
            // setCartCount(filteredData.length);
            setCartCount((prevCartCount) => prevCartCount + filteredData.length);
        } else {
            // Clear selectedRows and cartItems
            setSelectedRows([]);
            setCartItems([]);
            // Update cart count
            setCartCount(orderData.length);
        }
    };
    const handleChekbox = (e, row) => {
        if (e.key === "Enter") {
            e.target.checked = true;
            const checked = e.target.checked;
            const selectedRow = row.original;
            if (selectedRow) {
                if (checked) {
                    setSelectedRows((prevSelectedRows) => [...prevSelectedRows, selectedRow]);
                    setCartItems((prevCartItems) => [...prevCartItems, selectedRow]);
                    setCartCount((prevCartCount) => prevCartCount + 1);
                    console.log(`Cart Items:`, [...cartItems, selectedRow]);
                    console.log("Order data:", orderData);
                }
            }
        }
    };
    // const handleSelectRow = (e, row) => {
    //     const checked = e.target.checked;
    //     const selectedRow = row.original;
    //     if (selectedRow) {
    //         // Check if selectedRow is defined
    //         if (checked) {
    //             // Calculate total price of items in the cart
    //             var totalPriceOfItems = selectedRow.cost_price;

    //             // Update total state
    //             setTotal((prevTotal) => {
    //                 return prevTotal + calculatePrice() + totalPriceOfItems;
    //             });
    //             setSelectedRows((prevSelectedRows) => [...prevSelectedRows, selectedRow]);
    //             // Add the selected item to the cart
    //             setCartItems((prevCartItems) => [...prevCartItems, selectedRow]);

    //             // Update cart count
    //             setCartCount((prevCartCount) => prevCartCount + 1);
    //             // console.log(`Item ${selectedRow.name} added to cart.`);
    //             console.log(`Cart Items:`, [...cartItems, selectedRow]);
    //             console.log("Order data:", orderData[0]);
    //         } else {
    //             setSelectedRows((prevSelectedRows) =>
    //                 prevSelectedRows.filter((item) => item.item_id !== selectedRow.item_id)
    //             );
    //             // Remove the deselected item from the cart
    //             setCartItems((prevCartItems) =>
    //                 prevCartItems.filter((item) => item.item_id !== selectedRow.item_id)
    //             );
    //             var totalPriceOfItems = parseFloat(selectedRow.cost_price);

    //             // Update total state
    //             setTotal((prevTotal) => {
    //                 return prevTotal - totalPriceOfItems;
    //             });

    //             // Update cart count
    //             setCartCount((prevCartCount) => prevCartCount - 1);
    //             // console.log(`Item ${selectedRow.name} removed from cart.`);
    //             console.log(`Cart Items:`, cartItems.filter((item) => item.item_id !== selectedRow.item_id));
    //             console.log("Order data:", orderData[0]);
    //         }
    //     }
    // };

    // const handleSelectRow = (e, row) => {
    //     const checked = e.target.checked;
    //     const selectedRow = row.original;
    //     if (selectedRow) {
    //         if (checked) {
    //             // Calculate total price of items in the cart
    //             var totalPriceOfItems = parseFloat(selectedRow.cost_price).toFixed(2);

    //             // Update total state
    //             setTotal((prevTotal) => {

    //                 const newTotal = parseFloat(prevTotal).toFixed(2) + totalPriceOfItems;
    //                 // Round the result to two decimal places
    //                 return newTotal;
    //             });

    //             setSelectedRows((prevSelectedRows) => [...prevSelectedRows, selectedRow]);
    //             // Add the selected item to the cart
    //             setCartItems((prevCartItems) => [...prevCartItems, selectedRow]);

    //             // Update cart count
    //             setCartCount((prevCartCount) => prevCartCount + 1);
    //             // console.log(`Item ${selectedRow.name} added to cart.`);
    //             console.log(`Cart Items:`, [...cartItems, selectedRow]);
    //             console.log("Order data:", orderData[0]);
    //         } else {
    //             setSelectedRows((prevSelectedRows) =>
    //                 prevSelectedRows.filter((item) => item.item_id !== selectedRow.item_id)
    //             );
    //             // Remove the deselected item from the cart
    //             setCartItems((prevCartItems) =>
    //                 prevCartItems.filter((item) => item.item_id !== selectedRow.item_id)
    //             );

    //             var totalPriceOfItems = parseFloat(selectedRow.cost_price);

    //             // Update total state
    //             // Update total state
    //             setTotal((prevTotal) => {
    //                 // Round the total to two decimal places
    //                 const newTotal = (prevTotal - calculatePrice() - totalPriceOfItems).toFixed(2);
    //                 return parseFloat(newTotal); // Convert back to a floating-point number
    //             });


    //             // Update cart count
    //             setCartCount((prevCartCount) => prevCartCount - 1);
    //             // console.log(`Item ${selectedRow.name} removed from cart.`);
    //             console.log(`Cart Items:`, cartItems.filter((item) => item.item_id !== selectedRow.item_id));
    //             console.log("Order data:", orderData[0]);
    //         }
    //     }
    // };

    // const handleSelectRow = (e, row) => {
    //     const checked = e.target.checked;
    //     const selectedRow = row.original;

    //     if (selectedRow) {
    //         if (checked) {
    //             // Calculate total price of the selected item
    //             const totalPriceOfItem = parseFloat(selectedRow.cost_price);

    //             // Update total state by adding the price of the selected item
    //             setTotal((prevTotal) => prevTotal + totalPriceOfItem);

    //             // Add the selected item to cartItems and update cart count
    //             setSelectedRows((prevSelectedRows) => [...prevSelectedRows, selectedRow]);
    //             setCartItems((prevCartItems) => [...prevCartItems, selectedRow]);
    //             setCartCount((prevCartCount) => prevCartCount + 1);
    //         } else {
    //             // Remove the deselected item from cartItems and update cart count
    //             setSelectedRows((prevSelectedRows) =>
    //                 prevSelectedRows.filter((item) => item.item_id !== selectedRow.item_id)
    //             );
    //             setCartItems((prevCartItems) =>
    //                 prevCartItems.filter((item) => item.item_id !== selectedRow.item_id)
    //             );
    //             setCartCount((prevCartCount) => prevCartCount - 1);

    //             // Calculate total price of the deselected item
    //             const totalPriceOfItem = parseFloat(selectedRow.cost_price);

    //             // Update total state by subtracting the price of the deselected item
    //             setTotal((prevTotal) => prevTotal - totalPriceOfItem);
    //         }
    //     }
    // };
    const handleSelectRow = (e, row) => {
        const checked = e.target.checked;
        const selectedRow = row.original;
        const cakePrice = parseFloat(calculatePrice());
        setCakePrice(cakePrice);
        if (selectedRow) {
            const totalPriceOfItem = parseInt(selectedRow.unit_price) * parseInt(selectedRow.receiving_quantity);
            if (checked) {
                // Update total state by adding the price of the selected item and calculated price
                if(originalTotal == 0){
                    setTotal((prevTotal) => (parseInt(prevTotal) + totalPriceOfItem + parseInt(calculatePrice())));
                    setOriginalTotal((prevTotal) => (parseInt(prevTotal) + totalPriceOfItem + parseInt(calculatePrice())));
                }else{
                    setTotal((prevTotal) => (parseInt(prevTotal) + totalPriceOfItem ));
                    setOriginalTotal((prevTotal) => (parseInt(prevTotal) + totalPriceOfItem ));

                }
               

                // Add the selected item to cartItems and update cart count
                setSelectedRows((prevSelectedRows) => [...prevSelectedRows, selectedRow]);
                setCartItems((prevCartItems) => [...prevCartItems, selectedRow]);
                setCartCount((prevCartCount) => prevCartCount + 1);
            } else {
                // Update total state by subtracting the price of the deselected item and calculated price
                setTotal((prevTotal) => (parseInt(prevTotal) - totalPriceOfItem ));
                setOriginalTotal((prevTotal) => (parseInt(prevTotal) - totalPriceOfItem ));

                // Remove the deselected item from cartItems and update cart count
                setSelectedRows((prevSelectedRows) =>
                    prevSelectedRows.filter((item) => item.item_id !== selectedRow.item_id)
                );
                setCartItems((prevCartItems) =>
                    prevCartItems.filter((item) => item.item_id !== selectedRow.item_id)
                );
                setCartCount((prevCartCount) => prevCartCount - 1);
            }
        }
    };



    // useEffect(() => {
    //     // if (originalTotal === 0) {
    //     //     console.log("Orignal: ", originalTotal);
    //     // }
    //     setOriginalTotal(total);
    // }, [total, originalTotal]);


    const handleCategoryChange = (e) => {
        setFilterCategory(e.target.value);
    };

    const handleNameFilterChange = (e) => {
        setFilterName(e.target.value);
    };

    // const handleQuantityChange = (e, row) => {
    //   const newQuantity = parseInt(e.target.value);
    //   if (!isNaN(newQuantity) && newQuantity >= 0) {
    //     const newData = data.map((item) =>
    //       item.item_id === row.original.item_id ? { ...item, receiving_quantity: newQuantity } : item
    //     );
    //     setData(newData);

    //     const updatedCartItems = cartItems.map((item) =>
    //       item.item_id === row.original.item_id ? { ...item, receiving_quantity: newQuantity } : item
    //     );
    //     setCartItems(updatedCartItems);
    //     console.log(`Cart Items:`, updatedCartItems);
    //   }
    // };
    // const handleQuantityChange = (e, row) => {
    //     const newQuantity = parseFloat(e.target.value);
    //     if (!isNaN(newQuantity) && newQuantity >= 0) {
    //         const formattedQuantity = newQuantity.toFixed(3); // Ensure three decimal places
    //         const newData = data.map((item) =>
    //             item.item_id === row.original.item_id ? { ...item, receiving_quantity: formattedQuantity } : item
    //         );
    //         setData(newData);

    //         const updatedCartItems = cartItems.map((item) =>
    //             item.item_id === row.original.item_id ? { ...item, receiving_quantity: formattedQuantity } : item
    //         );
    //         setCartItems(updatedCartItems);
    //         console.log(`Cart Items Quantit Updated:`, updatedCartItems);
    //         console.log("Order data:", orderData);
    //     }
    // };
    const handleQuantityChange = (e, row) => {
        let newQuantity = e.target.value;
        if(newQuantity == ''){
            newQuantity = 1;
        }
        if (!isNaN(parseInt(newQuantity)) && newQuantity >= 0) {
            console.log(newQuantity)
            const formattedQuantity = newQuantity; // Ensure three decimal places
            const newData = data.map((item) =>
                item.item_id === row.original.item_id ? { ...item, receiving_quantity: formattedQuantity } : item
            );
            setData(newData);

            const totalPriceOfItem = parseFloat(row.original.unit_price) * newQuantity;

            const updatedCartItems = cartItems.map((item) =>
                item.item_id === row.original.item_id ? { ...item, receiving_quantity: formattedQuantity } : item
            );
            setCartItems(updatedCartItems);

            // Calculate the change in total price due to quantity change
            const priceChange = totalPriceOfItem - (parseFloat(row.original.unit_price) * parseFloat(row.original.receiving_quantity));

            // Update total state by adding the price change
            setTotal((prevTotal) => (parseFloat(prevTotal) + priceChange).toFixed(2));
        }
    };


    const handleQuantityChangeCartModal = (e, row) => {
        let newQuantity = e;
        if(newQuantity == ''){
            newQuantity = 1;
        }
        if (!isNaN(parseInt(newQuantity)) && newQuantity >= 0) {
            const formattedQuantity = newQuantity; // Ensure three decimal places
            const newData = data.map((item) =>
                item.item_id === row.item_id ? { ...item, receiving_quantity: formattedQuantity } : item
            );
            setData(newData);

            const totalPriceOfItem = parseFloat(row.unit_price) * newQuantity;

            const updatedCartItems = cartItems.map((item) =>
                item.item_id === row.item_id ? { ...item, receiving_quantity: formattedQuantity } : item
            );
            setCartItems(updatedCartItems);

            // Calculate the change in total price due to quantity change
            const priceChange = totalPriceOfItem - (parseFloat(row.unit_price) * parseFloat(row.receiving_quantity));

            // Update total state by adding the price change
            setTotal((prevTotal) => (parseFloat(prevTotal) + priceChange).toFixed(2));
        }
    };

    const handleQuantityKeyPress = (e, row) => {
        if (e.key === 'Enter') {
            e.target.blur(); // Remove focus from the input field
        }
    };

    const filteredData = useMemo(() => {
        let filtered = data;
        if (filterCategory) {
            filtered = filtered.filter((item) => item.category === filterCategory);
        }
        if (filterName) {
            filtered = filtered.filter((item) =>
                item.name.toLowerCase().includes(filterName.toLowerCase())
            );
        }
        return filtered;
    }, [data, filterCategory, filterName]);

    const uniqueCategories = useMemo(() => {
        const categories = data.map((item) => item.category);
        return Array.from(new Set(categories));
    }, [data]);

    const columns = useMemo(
        () => [
            {
                Header: (
                    <input
                        type="checkbox"
                        onChange={handleSelectAll}
                        checked={selectAll}
                    />
                ),
                id: 'checkbox',
                Cell: ({ row }) => (
                    <input
                        type="checkbox"
                        // onKeyDown={(e) => handleChekbox(e, row)}
                        onChange={(e) => handleSelectRow(e, row)}
                        checked={selectedRows.some(
                            (selectedRow) => selectedRow.item_id === row.original.item_id
                        )}
                    />
                ),
            },
            {
                Header: 'Quantity',
                accessor: 'receiving_quantity',
                Cell: ({ row }) => (
                    <input
                        type="number"
                        min={1}
                        value={row.original.receiving_quantity}
                        disabled={!selectedRows.some(selectedRow => selectedRow.item_id === row.original.item_id)}
                        onChange={(e) => {
                            if (selectedRows.some(selectedRow => selectedRow.item_id === row.original.item_id)) {
                                handleQuantityChange(e, row);
                            }
                        }}
                        // onKeyDown={(e) => handleQuantityKeyPress(e, row)}
                    />
                ),
            },
            { Header: 'Price', accessor: 'unit_price' },
            { Header: 'ID', accessor: 'item_id' },
            { Header: 'Name', accessor: 'name' },
            { Header: 'Category', accessor: 'category' },
        ],
        [selectedRows, selectAll]
    );

    const {
        getTableProps,
        getTableBodyProps,
        headerGroups,
        page,
        nextPage,
        previousPage,
        canNextPage,
        canPreviousPage,
        state: { pageIndex, pageSize },
        gotoPage,
        pageCount,
        prepareRow,
        setPageSize,
        setFilter,
    } = useTable(
        {
            columns,
            data: filteredData,
            initialState: { pageIndex: 0, pageSize: 10 },
        },
        useFilters,
        usePagination
    );

    const handlePageSizeChange = (e) => {
        const newSize = Number(e.target.value);
        setPageSize(newSize);
        if (pageIndex >= Math.ceil(filteredData.length / newSize)) {
            gotoPage(Math.ceil(filteredData.length / newSize) - 1);
        }
    };

    useEffect(() => {
        setFirstPageIndex(pageIndex);
    }, [pageIndex]);



    const branch_options = () => {
        return this.orderData[0].data.branches.map(item => {
            if (item.is_current) {
                return { 'value': item.id, 'label': `${item.name} - Current`, 'id': item.id, 'code': item.code }
            }
            return { 'value': item.id, 'label': item.name, 'id': item.id, 'code': item.code }

        })

    }


    const handlePopState = () => {
        // Push a new state into the history stack to prevent going back
        window.history.pushState(null, null, window.location.href);
    };
    window.addEventListener('popstate', handlePopState);

    const submitForm = async (e) => {
        e.preventDefault();

        e.preventDefault();
        if (cartItems.length <= 0) {
            alert.error("Please Select Item");
        }
        else if (orderData.length <= 0) {
            alert.error("Order details are requireed");

        }
        else {
            const {
                productId,
                orderType,
                paymentType,
                orderPriority,
                orderCatagory,
                selectedFlavours,
                selectedMaterials,
                assignedTo,
                salesmanName,
                customerName,
                customerEmail,
                customerPhone,
                productWeight,
                productPrice,
                productQuantity,
                productAdvanced,
                productRemarks,
                customerRemarks,
                customOrder,
                currentBranchIndex,
                imageFile,
                deliveryDate,
                deliveryTime,
                discount,
                is_custom,
                photo_id,
                custom_image,
                product_price,

            } = orderData[0];

            try {
                let data = new FormData();
                let _temp = "";
                data.append("customer_phone", customerPhone);
                data.append("weight", productWeight);
                data.append("quantity", productQuantity);
                data.append("total_price", cakePrice);
                data.append("advance_price", advance);
                data.append("payment_type", paymentType.value);
                data.append("order_type", orderType.value);
                data.append("remarks", productRemarks);
                data.append("instructions", customerRemarks);
                data.append("assigned_to", assignedTo.code);
                selectedFlavours.forEach((flavour) => _temp = _temp + `${flavour.falvourId},`);
                _temp = _temp.substring(0, _temp.length - 1);
                data.append("flavour_id", _temp);
                _temp = "";
                selectedMaterials.forEach((material) => _temp = _temp + `${material.materialId},`);
                _temp = _temp.substring(0, _temp.length - 1);
                data.append("material_id", _temp);
                data.append("priority", orderPriority.value);
                data.append("payment_status", 0);
                data.append("order_status", 'Un-Processed');
                data.append("delivery_date", deliveryDate);
                data.append("delivery_time", deliveryTime);
                data.append("branch_id", assignedTo.id);
                data.append("branch_code", assignedTo.code);
                data.append("salesman", salesmanName);
                data.append("customer_name", customerName);
                data.append("user_id", 0);
                data.append("discount", discount)
                data.append("is_active", 1);
                data.append("server_sync", 0);
                data.append("live_synced", 0);
                data.append("product_id", productId);
                if (customOrder) {
                    data.append("is_custom", 1);
                    data.append("photo_id", 0);
                    data.append("custom_image", imageFile);
                    data.append("product_price", productPrice);
                } else {
                    data.append("is_custom", 0);
                    data.append("product_price", productPrice);
                    data.append("photo_id", photo_id);
                }
                data.append("cartItems", JSON.stringify(cartItems))
                console.log(data)
                let result = await axios.post('/positems/save', data);

                console.log('submitted::', result);
                if (result.status > 200) {
                    //server not connected or could not responde properly, internet err probably
                    alert.error("Server not Connected");

                } else {
                    //server responded, check server message
                    if (result.data.status == "Success") {
                        //saved successfully

                        setCartCount(0);
                        setSelectedRows([]);
                        setSelectAll(false);
                        console.log("Success");

                        alert.success("Order has been created.")
                        // history.push(`/print_order/${result.data.payLoad}`)
                        window.location.href = `/print_order/${result.data.payLoad}`;
                        window.history.pushState(null, null, window.location.href);

                    } else {
                        //failed to save for some reason
                        console.log("Error")

                        alert.error(result.data.statusMessage)
                    }
                }
            } catch (e) {
                console.log('err axios post:', e);
            }
        }



    }

    return (

        <div className="inner_bg">
            <div id="wrapper">
                <header id="header" className="clearfix">

                    <nav className="inner_nav">
                        <ul>
                            <li><Link to={`/home`}>Main Menu</Link></li>
                            <li>»</li>
                            <li><Link to={`/categories`}>Events</Link></li>
                            <li>»</li>
                            <li>»</li>
                            <li>Place Order</li>
                        </ul>
                    </nav>
                    <div className="logo"> <a href="#"><img src="/images/logo.png" alt="" /></a> </div>
                </header>
                <section id="body">
                    <div className="inner_container">
                        <div className="place_order_container clearfix">
                            <h3>Select Product</h3>
                            <form onSubmit={submitForm}>
                                <div className="place_order_form">
                                    <div className="container mt-4">
                                        {loading ? (
                                            <div>Loading...</div>
                                        ) : (
                                            <div>
                                                <div className="d-flex mb-3 align-items-center" style={{ textAlign: 'center' }}>
                                                    <div className="flex-grow-1">
                                                        <input
                                                            type="text"
                                                            value={filterName}
                                                            onChange={handleNameFilterChange}
                                                            placeholder="Search by name"
                                                            className="form-control-sm mb-2"
                                                            style={{  
                                                                width:'50%',  
                                                                height:'20%',
                                                                margin:1,
                                                                fontSize: '20px', 
                                                                fontWeight: 'bold', 
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="d-flex mb-3 align-items-center">
                                                <div className="flex-grow-1">
                                                <select
                                                    value={filterCategory}
                                                    onChange={handleCategoryChange}
                                                    className="form-select-sm mb-2"
                                                >
                                                    <option value="">All Categories</option>
                                                    {uniqueCategories.map((category, index) => (
                                                        <option key={index} value={category}>
                                                            {category}
                                                        </option>
                                                    ))}
                                                </select>  
                                                </div>      
                                                <div className="flex-grow-0">
                                                        <select
                                                            value={pageSize}
                                                            onChange={handlePageSizeChange}
                                                            className="form-select-sm mb-2"
                                                        >
                                                            <option value={10}>10</option>
                                                            <option value={25}>25</option>
                                                            <option value={50}>50</option>
                                                            <option value={100}>100</option>
                                                        </select>
                                                    </div>
                                                    <div className="flex-grow-0 ml-2">
                                                        {/* <button
                                                            className="btn btn-primary btn-sm"
                                                            onClick={(e) => e.preventDefault()}
                                                        >
                                                            <FontAwesomeIcon icon={faShoppingCart} /> {`(${cartCount})`}
                                                        </button> */}

{cartButton}

{/* Render the cart modal if showCartModal is true */}
{showCartModal && (
    <CartModal
        cartItems={cartItems}
        handleQuantity={handleQuantityChangeCartModal}
        removeItem={removeFromCart}
        handleClose={handleCloseCartModal}
    />
)}



                                                    </div> 
                                                    </div>
                                                {filteredData.length === 0 ? ( // Render message if filteredData is empty
                                                    <div className='text-center'>No items record</div>
                                                ) : (
                                                    <table {...getTableProps()} className="table table-striped">
                                                        <thead>
                                                            {headerGroups.map((headerGroup) => (
                                                                <tr {...headerGroup.getHeaderGroupProps()}>
                                                                    {headerGroup.headers.map((column, index) => (
                                                                        <th {...column.getHeaderProps()} key={index}>
                                                                            {column.render('Header')}
                                                                        </th>
                                                                    ))}
                                                                </tr>
                                                            ))}
                                                        </thead>
                                                        <tbody {...getTableBodyProps()}>
                                                            {page.map((row) => {
                                                                prepareRow(row);
                                                                return (
                                                                    <tr {...row.getRowProps()}>
                                                                        {row.cells.map((cell, index) => (
                                                                            <td {...cell.getCellProps()} key={index}>
                                                                                {cell.render('Cell')}
                                                                            </td>
                                                                        ))}
                                                                    </tr>
                                                                );
                                                            })}
                                                        </tbody>
                                                    </table>
                                                )}
                                                <div className="d-flex justify-content-between">
                                                    <div>
                                                        <button
                                                            onClick={(e) => {
                                                                e.preventDefault()
                                                                if (pageIndex > 0) {
                                                                    setFirstPageIndex(pageIndex - 1);
                                                                    previousPage();
                                                                }
                                                            }}
                                                            disabled={!canPreviousPage}
                                                            className="btn btn-primary"
                                                        >
                                                            Previous
                                                        </button>
                                                        
                                                        {Array.from(
                                                            { length: Math.min(pageCount - firstPageIndex, 3) },
                                                            (_, i) => (
                                                                <button
                                                                    key={i + firstPageIndex}
                                                                    onClick={(e) => {
                                                                        e.preventDefault();
                                                                        gotoPage(firstPageIndex + i);
                                                                        setFirstPageIndex(firstPageIndex + i);
                                                                    }}
                                                                    className={`btn btn-primary mx-1 ${pageIndex === i + firstPageIndex ? 'active' : ''
                                                                        }`}
                                                                >
                                                                    {i + firstPageIndex + 1}
                                                                </button>
                                                            )
                                                        )}
                                                        <button
                                                            onClick={(e) => {
                                                                e.preventDefault();
                                                                if (pageIndex < pageCount - 1) {
                                                                    setFirstPageIndex(pageIndex + 1);
                                                                    nextPage();
                                                                }
                                                            }}
                                                            disabled={!canNextPage}
                                                            className="btn btn-primary"
                                                        >
                                                            Next
                                                        </button>
                                                    </div>
                                                    <div>
                                                        Page{' '}
                                                        <strong>
                                                            {pageIndex + 1} of {pageCount}
                                                        </strong>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p>
                                                        Showing {pageIndex * pageSize + 1}-
                                                        {Math.min((pageIndex + 1) * pageSize, filteredData.length)} of{' '}
                                                        {filteredData.length} rows
                                                    </p>
                                                </div>
                                            </div>
                                        )}
                                    </div>


                                    <div className="row align-items-center p-5">
                                        <div className="col-3">
                                            <label  style={{  fontSize: 'medium', // Set font size to small
                                                              fontWeight: 'bold', // Make the text bold
                                                          }}>Advance Payment </label>
                                            <input
                                                type="number"
                                                onChange={handleAdvance}
                                                value={advance}
                                                min={0}
                                                max={total}
                                                placeholder="Advance Payment"
                                                className="form-control-sm mb-2"
                                                style={{      margin:5,
                                                    fontSize: 'medium', // Set font size to small
                                                    fontWeight: 'bold', // Make the text bold
                                                }}
                                            />
                                        </div>
                                        <div className="col-1"></div>
                                        <div className="col-3">
                                            <label  style={{  fontSize: 'medium', // Set font size to small
                                                              fontWeight: 'bold', // Make the text bold
                                                          }}>Total </label>
                                            <input
                                                type="text"
                                                value={total-advance}
                                                min={0}
                                                disabled
                                                className="form-control-sm mb-2"
                                                style={{      margin:5,
                                                              fontSize: 'medium', // Set font size to small
                                                              fontWeight: 'bold', // Make the text bold
                                                          }}
                                            />
                                        </div>
{/* 
                                        <div className="col-3">
                                            <h4>Total: <span className="text-primary">{total} Rs</span> </h4>

                                        </div> */}
                                        
                                        <div className="col-4 ml-auto">
                                            <div className="buttons">

                                                <input disabled={enableBtn} name="btnSubmit" type="submit" className="btn2" value="Order Now" />
                                            </div>
                                        </div>

                                    </div>
                                </div>


                            </form>

                            {/* <LunchKitItems /> */}

                        </div>
                    </div>
                </section>
            </div>
        </div>
    );
});

export default MixOrderItems;