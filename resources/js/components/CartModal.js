import React, { useState } from 'react';

const CartModal = ({ cartItems, removeItem, handleClose }) => {
  const [quantityValues, setQuantityValues] = useState(
    cartItems.reduce((acc, item) => {
      acc[item.item_id] = item.receiving_quantity;
      return acc;
    }, {})
  );

  const updateQuantity = (itemId, newQuantity) => {
    setQuantityValues(prevState => ({
      ...prevState,
      [itemId]: newQuantity
    }));
  };

  return (
    <div className="modal" style={{ display: "block", backgroundColor: "rgba(0, 0, 0, 0.5)" }}>
      <div className="modal-dialog">
        <div className="modal-content">
          <div className="modal-header">
            <h5 className="modal-title">Cart Items</h5>
            <button type="button" className="btn-close" onClick={handleClose}></button>
          </div>
          <div className="modal-body">
            {cartItems.length === 0 ? (
              <p>No Items in Cart</p>
            ) : (
              <table className="table">
                <thead>
                  <tr style={{fontSize:'14px'}}>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th >Actions</th> {/* Adjusting width for Actions column */}
                  </tr>
                </thead>
                <tbody>
                  {cartItems.map((item) => (
                    <tr style={{fontSize:'14px'}} key={item.item_id}>
                      <td>{item.item_id}</td>
                      <td>{item.name}</td>
                      <td>{item.category}</td>
                      <td>
                        <input
                        className='w-75'
                          type="number"
                          value={quantityValues[item.item_id]}
                          onChange={(e) => updateQuantity(item.item_id, parseInt(e.target.value))}
                          min="0"
                        />
                      </td>
                      <td>
                        <button className="btn btn-danger" onClick={() => removeItem(item)}>Remove</button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
              
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default CartModal;
